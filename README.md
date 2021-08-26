# Hydrogen Source Indexer

In short: A tool to create an JSON index file for a collection of modules, usable with the Hydrogen framework.

## Introduction

In the world of Hydrogen Framework, there a modules which can be installed from one or many modules sources.
A module source is a set of Hydrogen modules, produced, collected or maintained by a company or maybe you.

Modules can be installed using the tool [Hymn](https://github.com/CeusMedia/Hymn).
To be abled to scan for existing modules, a project has a hymn configuration file with registered sources.
These sources need to be installed within the project using composer.

Since some libraries are collections with a higher number of modules, the indexing process can be made faster if the source has an index.
This tool can create such an index, by collecting all necessary information into a JSON file.

## Usage

Let's say you have:

- a project using the Hydrogen Framework
- used composer to install the framework
- used composer to install the default module source [Hydrogen Modules](https://github.com/CeusMedia/HydrogenModules) *(optiona)*
- have developed own modules, which shall be reusable
- decided to create an own module source
- have collected your set of modules in a VCS repository (GitHub, GitLab etc.)
- created a composer file within the module source reposity
- made your module library availabe via Packagist *(optional)*

### Installation

Check out your repository in dev mode into a working directory:
```
composer create-project my-vendor/my-hydrogen-module-source
```
You now have your modules installed in the current directory.
```
cd my-hydrogen-module-source
ls -lA
```
Now, add the module source indexer as a package in dev mode: 

```
composer require --dev ceus-media/hydrogen-source-indexer
```

### Creation

To create the JSON index, you can do:
```
php vendor/ceus-media/hydrogen-source-index/index.php json
```
This will create the index.json in the directory.

Furthermore, you can create a HTML index (index.html) by:
```
php vendor/ceus-media/hydrogen-source-index/index.php html
```
You can add both files to the repository.
While the JSON file can be used by Hymn, the HTML index is for usability and can be viewed in a browser to get an overview over the modules within the source.



