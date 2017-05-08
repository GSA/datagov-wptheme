/* global describe, expect, it, jest */

import { VectorMarkers } from '../src/'

jest.unmock('leaflet')

function addMarker(map, options) {
  const marker = VectorMarkers.icon(options)
  L.marker([48.15491,11.54183], { icon: marker }).addTo(map)
}

describe('VectorMarkers', () => {
  let map, mapElement

  beforeEach(function() {
    mapElement = document.createElement('div')
    mapElement.id = 'map'
    document.body.appendChild(mapElement)
    map = L.map('map', { center: [48.15491,11.54183], zoom: 13 })
  })

  afterEach(() => {
    document.body.removeChild(mapElement)
  })

  it('adds a layer to the map', () => {
    addMarker(map, { icon: 'coffee', markerColor: 'red' })
    expect(Object.keys(map._layers).length).toEqual(1)
  })

  it('adds the coffee marker to the map', () => {
    addMarker(map, { icon: 'coffee', markerColor: 'red' })
    expect(document.getElementsByClassName('fa-coffee').length).toEqual(1)
  })
})
