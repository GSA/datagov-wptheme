# Data.gov  

[Data.gov](http://data.gov) is an open data website created by the [U.S. General Services Administration](https://github.com/GSA/) that is based on two robust open source projects: [CKAN](http://ckan.org) and [WordPress](http://wordpress.org). The data catalog at [catalog.data.gov](https://catalog.data.gov) is powered by CKAN, while the content seen at [Data.gov](http://Data.gov) is powered by WordPress.  
        
**This repository provides the [issue tracker](https://github.com/GSA/data.gov/issues) for all code, bugs, and feature requests related to Data.gov.** Currently the repository is only used for source version control on the code for the WordPress template, but you will also find pointers to the relevant CKAN code and additional resources documented below.

### CKAN

* **CKAN.** The Data.gov team recommends the [latest version of CKAN](http://ckan.org/developers/docs-and-download/).
* **Data.gov CKAN.** The code powering the Data.gov instance of CKAN. 
    * [release-datagov](https://github.com/GSA/ckan/tree/release-datagov) - The main development branch used for the current [catalog.data.gov](https://catalog.data.gov).
    * [GSA/ckanext-geodatagov](https://github.com/GSA/ckanext-geodatagov) - Most data.gov specific CKAN customizations are contained within this extension, but the extension also provides additional geospatial capabilities.  
    * [ckanext-datagovtheme](https://github.com/GSA/ckanext-datagovtheme) - The CKAN theme for catalog.data.gov
* **Extensions.** The Data.gov team has developed several CKAN extensions, but these are still in the process of being packaged for more widespread use. The [full list of installed extensions can be seen via the CKAN API](http://catalog.data.gov/api/util/status). Custom extensions include:
   * [GSA/ckanext-datajson](https://github.com/GSA/ckanext-datajson) - A CKAN extension for [Project Open Data](https://project-open-data.github.io) /data.json harvesting and publishing. 
   * [GSA/USMetadata](https://github.com/GSA/USMetadata) - A CKAN extension to support the [Project Open Data](https://project-open-data.github.io) metadata schema within the CKAN user interface. 
* **Other Tools**  
   * [GSA/ckan-php-client](https://github.com/GSA/ckan-php-client) - A CKAN php client for Data.gov.
   * [GSA/ckan-php-manager](https://github.com/GSA/ckan-php-manager) - A CKAN php manager for Data.gov.    
* **Deployment.** We are in the process of improving documentation and hope to provide build scripts and configurations for tools like [Vagrant](http://www.vagrantup.com/) to make setting up the Data.gov CKAN easier for others.  

### WordPress

* **WordPress.** The Data.gov team recommends the [latest version of WordPress](http://wordpress.org/download/).
* **Data.gov WordPress template.** The code powering the Data.gov WordPress template.
    * [GSA/data.gov](https://github.com/GSA/data.gov) or *this repository*. The source version control of the Data.gov WordPress template. The theme is provided in the `/themes/` folder. The theme is based on [roots.io](http://roots.io/starter-theme/).
* **Plugins.** See the routinely updated [plugins](plugins.md) page for a list of all the plugins used on [Data.gov](http://Data.gov).
    * [GSA/datagov-custom](https://github.com/GSA/datagov-custom) - Most data.gov specific customizations are contained within this extension
* **Deployment.** Download the [latest version of WordPress](http://wordpress.org/download/). This is a standard WordPress install, so please refer to the [WordPress Docs](http://codex.wordpress.org/Installing_WordPress). In the near future we hope to release the configuration for installing the Data.gov WordPress using [WordPress CLI](http://wp-cli.org/). 

### Additional Data.gov Resources
* **Data.gov/Developers.**  In addition to this repository, please be sure to look at the Data.gov Developers section for more updates and resources, including information on Data.gov's CKAN API: http://data.gov/developers/
* **Design.** Design assets for Data.gov.
    * [GSA/datagov-design](https://github.com/GSA/datagov-design) - The source graphic files for logo, icons, layout.
* **Communications.** Communication and publishing systems that power Data.gov.
    * [GSA/idm](https://github.com/GSA/idm) - Identity Management for Data.gov and related systems. 
    * [GSA/open311-simple-crm](https://github.com/GSA/open311-simple-crm) - A simple CRM application built with the Open311 API. 
* **Harvest Tools.** Tools to support Data.gov harvesting and compliance with the format and metadata schema requirements of Project Open Data. *Learn more at [Project Open Data](project-open-data.cio.gov).*
    * [GSA/enterprise-data-inventory](https://github.com/GSA/enterprise-data-inventory) - A CKAN based enterprise data management system for private and public data management available at [inventory.data.gov](inventory.data.gov).
    * [GSA/project-open-data-dashboard](https://github.com/GSA/project-open-data-dashboard) - An automated dashboard assessing agency and department compliance with Project Open Data.
* **Style Guide.** A content style guide for Data.gov.
    * [GSA/data.gov-styleguide](https://github.com/GSA/data.gov-styleguide) - A Style Guide for prose on Data.gov, heavily inspired by UK.gov's style guide.

## Ways to Contribute
We're so glad you're thinking about contributing to Data.gov!

Before contributing to Data.gov we encourage you to read our [CONTRIBUTING](https://github.com/GSA/data.gov/blob/master/CONTRIBUTING.md) guide, our [LICENSE](https://github.com/GSA/data.gov/blob/master/LICENSE.md), and our README (you are here), all of which should be in this repository. If you have any questions, you can email the Data.gov team at [datagov@gsa.gov](mailto:datagov@gsa.gov).


