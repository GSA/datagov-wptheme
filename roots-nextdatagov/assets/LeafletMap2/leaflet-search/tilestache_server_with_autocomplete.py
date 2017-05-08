import json
import marisa_trie
import os
import shutil
from operator import itemgetter

import TileStache
from werkzeug.serving import run_simple
from werkzeug.wrappers import Request, Response

from cartograph import Utils
from cartograph import Config

""" This is an example from a summer research project that adapted Leaflet search to work with a Tilestache tile server running on top of Werkzeug. I had to do a lot of fiddling around with it to get the autocomplete to work, so I thought the example might be helpful for other people"""


def run_server(path_cartograph_cfg, path_tilestache_cfg):
    """Server run function, you probably don't care about this part if all you care about is implementing search"""
    Config.initConf(path_cartograph_cfg)
 
    
    path_tilestache_cfg = os.path.abspath(path_tilestache_cfg)
    path_cache = json.load(open(path_tilestache_cfg, 'r'))['cache']['path']
    static_files =  { '/static': os.path.join(os.path.abspath('./web')) }

    if os.path.isdir(path_cache):
        assert(len(path_cache) > 5)
        shutil.rmtree(path_cache)

    app = CartographServer(path_tilestache_cfg, Config.get())
    run_simple('0.0.0.0', 8080, app, static_files=static_files)
   

class CartographServer(TileStache.WSGITileServer):

    def __init__(self, path_cfg, cartograph_cfg):
        TileStache.WSGITileServer.__init__(self, path_cfg)
        self.cartoconfig = cartograph_cfg
      
        #Reading in features from our particular dataset (Wikipedia articles that have popularities, coordinates, and names)
        self.popularityDict = Utils.read_features(
                                    self.cartoconfig.get("ExternalFiles", "names_with_id"),
                                    self.cartoconfig.get("GeneratedFiles","popularity_with_id"))
        xyDict = Utils.read_features(self.cartoconfig.get("GeneratedFiles", "article_coordinates"),
                                     self.cartoconfig.get("ExternalFiles", "names_with_id"),
                                     self.cartoconfig.get("GeneratedFiles", "zoom_with_id"),
                                     required=('x', 'y', 'name', 'maxZoom'))

        self.keyList = []
        self.tupleLocZoom = []

        self.titleLookupDict = dict()

        #Utils.readfeatures gives me a dict keyed by id#, this is going through it to extract and format the useful information"""
        for entry in xyDict:
            #x and y have to be flipped to get it to match up, quirk of our dataset
            y = float(xyDict[entry]['x'])
            x = float(xyDict[entry]['y'])
            title = xyDict[entry]['name']
            self.titleLookupDict[entry] = title
            #zoom is the location it shows at, it's passed through the search function so that I can make the map zoom to the proper location on the JavaScript side
            zoom = int(xyDict[entry]['maxZoom'])
            loc = [x, y]
            idnum = int(entry)
            #create tuple of info and a title, add them both to separate lists (they'll get zipped together eventually)
            locZoom = (x, y, zoom, idnum)
            lowertitle = unicode(title.lower(), 'utf-8')
           
            self.keyList.append(lowertitle)
            self.tupleLocZoom.append(locZoom)

        #after creating lists of all titles and location/zoom, zip them into a trie  - extracted to json format later
        fmt = "<ddii" #a tuple of double, double, int, string (x, y, zoom, regular case title)
        self.trie = marisa_trie.RecordTrie(fmt, zip(self.keyList, self.tupleLocZoom))

    def __call__(self, environ, start_response):
        
        path_info = environ.get('PATH_INFO', None)

        # if the user tried to search for something (this is done with url routing in the server config file)
        if path_info.startswith('/dynamic/search'):
                request = Request(environ)

                #get the thing the user searched for
                title = request.args['q']
                
                #trie autocomplete reponse
                results = self.trie.items(unicode(title))

                #empty list to hold tuples to sort
                tupleList = []
                #empty list to hold json-formatted results
                jsonList = []

                #extract values from tuple in trie - this is needed because it's autocomplete, so there are multiple results
                for item in results:
                    idnum = str(item[1][3])
                    titlestring = self.titleLookupDict[idnum]
                    pop = float(self.popularityDict[idnum]['popularity'])
                    x = item[1][0]
                    y = item[1][1]
                    locat = [x,y]
                    zoom = item[1][2]
                    itemTuple = (locat, zoom, titlestring, pop)
                    tupleList.append(itemTuple)

                sortedTupleList = sorted(tupleList, key=itemgetter(3))
                sortedTupleList.reverse()

                #creating the json for each item
                for item in sortedTupleList:
                    locat = item[0]
                    zoom = item[1]
                    titlestring = item[2]
                    rJsonDict = {"loc": locat, "title": titlestring, "zoom" : zoom}
                    jsonList.append(rJsonDict)
                
                
                response = Response (json.dumps(jsonList))
                response.headers['Content-type'] = 'application/json'
               
                #returning the response to the browser!
                return response(environ, start_response)
            

        else:
            return TileStache.WSGITileServer.__call__(self, environ, start_response)