<div class="sixteen columns">
    <div class="next-logo">
        <!--<div id="next-logo-title-bg" class="next-background"></div> -->
        <div class="next-object"><!--  <span id="next-logo-title">Data.Gov</span>  -->
            <img src="<?php echo get_bloginfo('template_directory'); ?>/images/datagov.png" alt="Data Gov Logo">
        </div>
    </div>
    <div class="next-stats">
        <div class="next-background"></div>
        <div id="rotate-stats" class="next-object" data-strings='
    { "targets" : ["91,101 datasets", "409 APIs", "349 apps","137 mobile apps","175 agencies"]}'>&nbsp;</div>
    </div>
    <div class="next-search">
        <div class="next-background"></div>
        <div class="next-object">
          <!--  <div class="next-search-label">
                <label class="next" for="next-search-box">Search</label>
            </div> -->
            <div class="next-search-icon">
                <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/search.png" alt="Primary Search">
            </div>
            <div class="next-search-input">
                <form method="get" action="http://catalog.data.gov/dataset">
                    <label for="search-textbox" class="hddn" title="Search Data.gov">Search Data.gov</label>
                    <input id="search-textbox" role="search" class="next" name="q" type="text" title="Start Searching"  onKeyUp="hidesearch();return false;"  >
                    <a href="#" id="bottle"  onMouseOver="displaysearch();return false;" ><span id="g-search-button"></span></a>
                    <div id="searchlist" style="display:none; ">
                        <label><input type="checkbox" id="SearchCatalog" name="" value="" checked="true">&nbsp;&nbsp;Search Data Catalog</label><br>
                        <label><input type="checkbox" id="SearchSite" name="SearchSite" value="SearchSite">&nbsp;&nbsp;Search Site Content</label><br>
                    </div>
                    <input id="next-search-submit" type="submit" />
                </form>
            </div>
        </div>
    </div>
    <div class="next-categories">
        <div class="next-background"></div>
        <div class="next-object">
            <ul>
                <li><a href="education">Education</a></li>
                <li><a href="energy">Energy</a></li>
                <li><a href="/consumer">Finance</a></li>
                <li><a href="development">Global Development</a></li>
                <li><a href="http://www.healthdata.gov" target="_blank">Health</a></li>
                <li><a href="research">Research</a></li>
                <li><a href="safety">Safety</a></li>
                <li><a href="communities">More communities</a></li>
            </ul>
        </div>
    </div>
</div> <!-- sixteen columns -->

<script>
    function comingSoon(){
        alert("This functionality coming soon.");
    }
</script>

<script>
    function comingSoonCommunity(){
        alert("This community is coming soon.");
    }
</script>
<script type="text/javascript">
    function displaysearch() {

        document.getElementById("searchlist").style.display = 'block';


    };
</script>
<script type="text/javascript">
    $('#search-textbox').blur(function() {
        $("#searchlist").hide()
    });

    $('#search-textbox').focus(function() {
        $("#searchlist").show()
    });
</script>
<script type="text/javascript">
    $('input[type="text"]').each(function(){

        this.value = $(this).attr('title');
        $(this).addClass('text-label');

        $(this).focus(function(){
            if(this.value == $(this).attr('title')) {
                this.value = '';
                $(this).removeClass('text-label');
            }
        });

        $(this).blur(function(){
            if(this.value == '') {
                this.value = $(this).attr('title');
                $(this).addClass('text-label');
            }
        });
    });
</script>
<script type="text/javascript">
    function hidesearch() {
        var e = document.getElementById("searchlist");
        if(e.style.display == 'block'){
            e.style.display = 'none';
        }

    };



</script>
