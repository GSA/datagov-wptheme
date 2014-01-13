Data.gov
==============
[Data.gov](http://data.gov) is based on two robust open source projects: [WordPress](http://wordpress.org) and [CKAN](http://ckan.org). The content seen at data.gov is powered by WordPress while the data catalog at catalog.data.gov is powered by CKAN. 

This repository provides the [issue tracker](https://github.com/GSA/data.gov/issues) for all code, bugs, and feature requests related to these websites. Currently the repository is only used for source version control on the code for the WordPress template, but you will also find pointers to the relevant CKAN code documented below.

In addition to this repository, please be sure to look at the Data.gov Developers section for more updates and resources: http://data.gov/developers/


## WordPress
The latest version of WordPress is available at: http://wordpress.org/download/

###Theme

The theme is provided in the `/themes/` folder. The theme is based on [roots.io](http://roots.io/starter-theme/).

###Plugins

Custom plugins are provided in the `/plugins` folder. See the [plugins](plugins.md) page for a list of all the plugins used.

###Exportables
The `/exportables/` folder contains advanced custom fields definitions in xml format that can be imported manually via acf plugin.

### Deployment
This is a standard WordPress install, so please refer to the [Wordpress Docs](http://codex.wordpress.org/Installing_WordPress). In the near future we hope to release the configuration for installing the Data.gov WordPress using [WordPress CLI](http://wp-cli.org/). 

## CKAN

The code used for the catalog.data.gov instance of CKAN is available on [Github](https://github.com/okfn/ckan/tree/release-datagov) but we recommend the [latest version of CKAN](http://ckan.org/developers/docs-and-download/). 

### Extensions

Data.gov has developed several CKAN extensions, but these are still in the process of being packaged for more widespread use. Most of these extentions are referenced in an [Ansible](http://www.ansibleworks.com/) [build script](https://github.com/okfn/ckanext-geodatagov/blob/dev/deployment/datagov-buildserver.yml), but additionally, there is an extension to support [data.json harvesting](https://github.com/FuhuXia/ckanext-datajson/tree/master). Please check back here or [contact us](https://www.data.gov/contact) for more details on the extensions used.

### Deployment

Please check back here or [contact us](https://www.data.gov/contact) for more information about our CKAN stack. We are in the process of improving documentation and hope to provide build scripts and configurations for tools like [Vagrant](http://www.vagrantup.com/) to make setting up the Data.gov CKAN easier for others.


##Contributing

In the spirit of free software, everyone is encouraged to help improve this project.

Here are some ways you can contribute:

- by using alpha, beta, and prerelease versions
- by reporting bugs
- by suggesting new features
- by translating to a new language
- by writing or editing documentation
- by writing specifications
- by writing code (**no pull request is too small**: fix typos in the user interface, add code comments, clean up inconsistent whitespace)
- by refactoring code
- by closing issues
- by reviewing pull requests

When you are ready, submit a [pull request](https://github.com/GSA/data.gov/pulls).

##Submitting an Issue

We use the [GitHub issue tracker](https://github.com/GSA/data.gov/issues) to track bugs and features. Before submitting a bug report or feature request, check to make sure it hasn't already been submitted. You can indicate support for an existing issue by voting it up. When submitting a bug report, please try to provide a screenshot that demonstrates the problem. 

If you've encountered a bug while working in your own developer environment or if you have more extensive technical details to provide, please consider linking to a [Gist](https://gist.github.com/) that includes the full error log and/or any details that may be necessary to reproduce the bug.

##License

This project constitutes a work of the United States Government and is not subject to domestic copyright protection under 17 USC § 105.

The project utilizes code licensed under the terms of the GNU General Public License and therefore is licensed under GPL v2 or later.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

Visit http://www.gnu.org/licenses/ to learn more about the GNU General Public License.