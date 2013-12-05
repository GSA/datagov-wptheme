<?php
                    while( have_posts() ) {
                        the_post();
                        ?>


      <?php the_title();?>

    <?php the_content();   ?>
    <?php }?>
<div class="map-gallery-wrap">
                            <?php
                            global $regional_map_results;
                            $mapinfo = array();
                            $groupinfo = array();
                            $groupmapinfo = array();
                            for($i=0;$i<count($regional_map_results);$i++){
                                if($regional_map_results[$i]["info"]["type"]=="Map"){
                                    $mapinfo[$i] = array_merge($regional_map_results[$i]["map_info"][0]);
                                }
                                if($regional_map_results[$i]["info"]["type"]=="Group"){
                                    $groupinfo[$i] = array_merge($regional_map_results[$i]["map_info"]);
                                }
                            }
                            for($j=0;$j<count($groupinfo);$j++){
                                unset($groupinfo[$j]["total_maps"]);
                                $groupmapinfo[] = array_merge($groupinfo[$j]);
                            }
                            $group = array();
                            foreach ($groupmapinfo as $array) {
                                $group = array_merge($group, $array);
                            }
                            $merged_maps_tosort = array_merge($mapinfo,$group);
                            $merged_maps = subval_sort($merged_maps_tosort,"title");
                            $total_maps = count($merged_maps);

                            //code for pagination
                            $mapsperpage = (get_option('arcgis_maps_per_page') != '') ? get_option('arcgis_maps_per_page') : '8';
                            if(isset($regional_map_results)) {
                                $total_pages = ceil($total_maps / $mapsperpage);
                            } else {
                                $total_pages = 1;
                                $total_maps = 0;
                            }
                            if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
                                $currentpage = (int) $_GET['currentpage'];
                            } else {
                                $currentpage = 1;
                            }
                            if ($currentpage > $total_pages) {
                                $currentpage  = $total_pages;
                            }
                            if ($currentpage < 1) {
                                $currentpage = 1;
                            }
                            $start = ($currentpage - 1) * $mapsperpage + 1;
                            $count =0;
                            $output = "";
                            for($i=$start-1; $i<$start-1+$mapsperpage; $i++){
                                if(isset($merged_maps[$i])) {
                                    $output .= '<div class="map-align">';
                                    $output .= '<a target=_blank href="'. $merged_maps[$i]["img_href"] . '">';
                                    $output .= '<img class="map-gallery-thumbnail" src="'. $merged_maps[$i]["img_src"] . '" title="' . $merged_maps[$i]["title"] .'">';

                                    $output .= '<div class="map-gallery-caption">'. $merged_maps[$i]["title"] . '</div>';
                                    $output .= '</a>';
                                    $output .= '</div>';
                                }
                                $count ++;
                            }
                            $output .= "</div><div class='item-list'><ul class='pager'>";
                            if($total_maps > $mapsperpage) {
                                $range = 10;
                                if ($currentpage > 1) {
                                    $output .= "<br clear='both'/><li class='pager-first first'><a href='?currentpage=1'><<< FIRST </a></li> ";
                                    $prevpage = $currentpage - 1;
                                    $output .= "<li class='pager-previous'><a href='?currentpage=$prevpage'>< PREVIOUS  </a> </li>";
                                }
                                for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
                                    if (($x > 0) && ($x <= $total_pages)) {
                                        if ($x == $currentpage) {
                                            if ($currentpage == 1) {
                                                $output .="<br clear='both'/><br/>";
                                            }
                                            if ($total_pages > 1) {
                                                $output .= "<li class='pager-current first'>$x</li>";
                                            }
                                        }
                                        else {
                                            $output .= "<li class='pager-item'><a href='?currentpage=$x'> $x </a></li>";
                                        }
                                    }
                                }
                                if ($currentpage != $total_pages) {
                                    $nextpage = $currentpage + 1;
                                    $output .= " <li class='pager-next'> <a href='?currentpage=$nextpage'>NEXT ></a></li> ";
                                    $output .= " <li class='pager-last last'><a href='?currentpage=$total_pages'>  LAST >>></a> </li>";
                                }
                            }
                            print $output;
                            ?>
                            </div>