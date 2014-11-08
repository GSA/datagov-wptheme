# Data.gov  

[Data.gov](http://data.gov) is a website created by the [U.S. General Services Administration](https://github.com/GSA/) based on two robust open source projects: [CKAN](http://ckan.org) and [WordPress](http://wordpress.org). The data catalog at [catalog.data.gov](catalog.data.gov) is powered by CKAN, while the content seen at [Data.gov](Data.gov) is powered by WordPress.  
        
**This repository provides the [Issue Tracker](https://github.com/GSA/data.gov/issues) for all code, bugs, and feature requests related to these websites.** Currently the repository is only used for source version control on the code for the WordPress template, but you will also find pointers to the relevant CKAN code and tools documented below.

### CKAN

* **CKAN.** The Data.gov team recommends the [latest version of CKAN](http://ckan.org/developers/docs-and-download/).
* **Data.gov CKAN.** The code powering the [Data.gov](Data.gov) instance of CKAN. 
    * [release-datagov](https://github.com/okfn/ckan/tree/release-datagov) - The code used for the current [catalog.data.gov](catalog.data.gov).
    * [GSA/ckan-php-client](https://github.com/GSA/ckan-php-client) - A CKAN php client for [Data.gov](Data.gov).
    * [GSA/ckan-php-manager](https://github.com/GSA/ckan-php-manager) - A CKAN php manager for [Data.gov](Data.gov).
* **Extensions.** The Data.gov team has developed several CKAN extensions, but these are still in the process of being packaged for more widespread use. Exentions include:
    * [GSA/ckanext-geodatagov](https://github.com/GSA/ckanext-geodatagov) - A CKAN extension for geospatial data harvesting. 
    * [GSA/ckanext-datajson](https://github.com/GSA/ckanext-datajson) - A CKAN extension for [Project Open Data](project-open-data.github.io) /data.json harvesting. 
    * [GSA/USMetadata](https://github.com/GSA/USMetadata) - A CKAN extention to support the [Project Open Data](project-open-data.github.io) metadata schema. 
    * [GSA/datagov-custom](https://github.com/GSA/datagov-custom) - A CKAN extension to created track the metrics of the number of harvest sources per agency.
* **Deployment.** We are in the process of improving documentation and hope to provide build scripts and configurations for tools like [Vagrant](http://www.vagrantup.com/) to make setting up the Data.gov CKAN easier for others.  

### WordPress

* **WordPress.** The Data.gov team recommends the [latest version of WordPress](http://wordpress.org/download/).
* **Data.gov WordPress template** – The code powering the Data.gov WordPress template.
    * [GSA/data.gov](https://github.com/GSA/data.gov) or *this repository*. The source version control of the Data.gov WordPress template. The theme is provided in the `/themes/` folder. The theme is based on [roots.io](http://roots.io/starter-theme/).
* **Plugins.** See the routinely updated [plugins](plugins.md) page for a list of all the plugins used on [Data.gov](Data.gov).
    * [GSA/wp-open311](https://github.com/GSA/wp-open311) - A WordPress Plugin to interact with Open311 API.
* **Custom.** Custom code for the Data.gov WordPress site.
    * [GSA/custom-post-view-generator](https://github.com/GSA/custom-post-view-generator) - [Data.gov](Data.gov)'s custom WordPress post view generator. 
* **Deployment.** Download the [latest version of WordPress](http://wordpress.org/download/). This is a standard WordPress install, so please refer to the [WordPress Docs](http://codex.wordpress.org/Installing_WordPress). In the near future we hope to release the configuration for installing the Data.gov WordPress using [WordPress CLI](http://wp-cli.org/). 

### Additional Data.gov Resources
* **Data.gov/Developers.**  In addition to this repository, please be sure to look at the Data.gov Developers section for more updates and resources, including information on Data.gov's CKAN API: http://data.gov/developers/
* **Design.** Design assests for [Data.gov](Data.gov).
    * [GSA/datagov-design](https://github.com/GSA/datagov-design) - The source graphic files for logo, icons, layout.
* **Communications.** Communication and publishing systems that power [Data.gov](Data.gov).
    * [GSA/idm](https://github.com/GSA/idm) - Identity Management for Data.gov and related systems. 
    * [GSA/open311-simple-crm](https://github.com/GSA/open311-simple-crm) - A simple CRM application built with the Open311 API. 
* **Harvest Tools.** Tools to support [Data.gov](Data.gov) harvesting and compliance with the format and metadata schema requirements of Project Open Data. *Learn more at [Project Open Data](project-open-data.github.io).*
    * [GSA/data_gov_json_validator](https://github.com/GSA/data_gov_json_validator) - A Validator for Project Open Data metadata schema /data.json. 
    * [GSA/enterprise-data-inventory](https://github.com/GSA/enterprise-data-inventory) - A CKAN based enterprise data management system for private and public data management available at [inventory.data.gov](inventory.data.gov).
    * [GSA/project-open-data-dashboard](https://github.com/GSA/project-open-data-dashboard) - An automated dashboard assessing agency and department compliance with Project Open Data.
* **Style Guide.** A content style guide for [Data.gov](Data.gov).
    * [GSA/data.gov-styleguide](https://github.com/GSA/data.gov-styleguide) - A Style Guide for prose on Data.gov, heavily inspired by UK.gov's style guide.

## Ways to Contribute

The Data.gov team manages all Data.gov updates, bugs, and features via GitHub's public [Issue Tracker](https://github.com/GSA/data.gov). In the spirit of open source software, everyone is encouraged to help improve this project. Here are some ways you can contribute:
- by reporting bugs
- by suggesting new features
- by translating to a new language
- by writing or editing documentation
- by writing specifications
- by writing code and documentation (**no pull request is too small**: fix typos, add code comments, clean up inconsistent whitespace)
- by reviewing [pull requests](https://github.com/GSA/data.gov/pulls).
- by closing issues

Issues labeled [`help wanted`](https://github.com/GSA/data.gov/labels/help%20wanted) make it easy for you to find ways you can contribute today. 

NOTE: Before submitting an [Issue](https://github.com/GSA/data.gov/issues), check to make sure it hasn't already been submitted. When submitting a bug report, please try to provide as much detail as possible, i.e. a screenshot or [Gist](https://gist.github.com/) that demonstrates the problem, your computer environment, and any relevant links. 

## Contact 

Please check back here or [contact us](https://www.data.gov/contact) for more details on the technologies used to create [Data.gov](Data.gov)

## License  

This project constitutes a work of the United States Government and is not subject to domestic copyright protection under 17 USC § 105.

The project utilizes code licensed under the terms of the GNU General Public License and therefore is licensed under GPL v2 or later.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

Visit http://www.gnu.org/licenses/ to learn more about the GNU General Public License.
