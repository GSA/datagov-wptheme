<?php
if (is_singular('applications')) {
    get_template_part('templates/content', 'applications');
} elseif (is_singular('challenge')) {
    get_template_part('templates/content', 'challenge');
} elseif (is_singular('events')) {
    get_template_part('templates/content', 'events');
} elseif (is_singular('arcgis_maps')) {
    get_template_part('templates/content', 'arcgis_maps');
} elseif (is_singular('regional_planning')) {
    get_template_part('templates/content', 'regional_planning');
} else {
    get_template_part('templates/content', 'single');
}