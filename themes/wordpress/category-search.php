<div class="sixteen columns">
    <div class="next-logo">
        <div id="next-logo-title-bg" class="next-background"></div>
        <div class="next-object">
        <span id="next-logo-title"><?php
            // do not show category if it Uncategorized
            $category = get_the_category();
            if ($category[0]->cat_name != 'Uncategorized') {
                echo $category[0]->cat_name;
            }
            ?></span>
        </div>
    </div>
    <div class="next-search">
        <div class="next-background"></div>
        <div class="next-object">
            <div class="next-search-label">
                <label class="next" for="next-search-box">Search</label>
            </div>
            <div class="next-search-icon">
                <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/search.png" alt="Category Search">
            </div>
            <div class="next-search-input">
                <form method="get" action="http://catalog.data.gov/dataset">
                    <label for="search-textbox" class="hddn" title="Search Data.gov">Search Data.gov</label>
                    <input id="search-textbox" role="search" class="next" name="q" type="text" title="Start Searching"  onKeyUp="hidesearch();return false;"  >
                    <a href="#" id="bottle" onMouseOver="displaysearch();return false;" ><span id="g-search-button"></span></a>
                    <div id="searchlist" style="display:none; ">
                        <label><input type="checkbox" id="SearchCatalog" name="" value="" checked="true">&nbsp;&nbsp;Search Data Catalog</label><br>
                        <label><input type="checkbox" id="SearchSite" name="SearchSite" value="SearchSite">&nbsp;&nbsp;Search Site Content</label><br>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- sixteen columns -->
<script type="text/javascript">
    function displaysearch() {

        document.getElementById("searchlist").style.display = 'block';


    };
</script>
<script type="text/javascript">
    $('#search-textbox').focus(function() {
        $("#searchlist").show();
    });
    $( document ).ready(function() {
        $( '#search-textbox' ).focusout( function() {
            $( '#searchlist' ).hover(
                    function() {
                        return;
                    },
                    function() {
                        $( '#searchlist' ).fadeOut( 'slow' );

                    });
        });
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
