Minimal requirements
================

- node 14.x.x
- npm 6.x.x
- composer 2
- wp-cli 2.5
- php 7.4
- mysql 8

Quick start

- create **.env** file from example **.env.example**
- install php packages and used plugins with composer. run **composer install** in theme folder
- install node packages. run **npm install** in theme folder
- Run **npm run watch** to start development server and begin development

Folder Structure
================

    resources/scss
    │
    ├── theme                               # Default theme styles
    │   ├── base                            # typography, colors, grid, breakpoints, fonts
    │   ├── components                      # dev components
    │   ├── layout                          # default layout blocks: footer, header, navigation, menu, forms
    │   ├── mixins                          # mixin, functions
    │   ├── vandors                         # Wordpress plugins rewrite styles
    │   │
    │   │
    ├── wp-dashboard                        # Admin styles
    │   ├── admin.scss                      # Note: @import by default only need styles in admin for gutenberg blocks, and some custom components (dev) need @import here
    │   └── ...                             # etc.
    └──

    resources/js
    │
    ├── modules                              # all scripts, header.js, modals.js, .etc
    ├── utils                                # helpers scripts
    └──


    templates
    │
    ├── page-style-guide/                    # style guide page, used most tags on the frontend
    ├── page-dev-components/                 # dev components, if you are creating a component, need include it here
    ├── page-gutenberg-blocks/               # all used gutenberg blocks on the site
    ├── page-gutenberg-blocks-demo/          # all blocks from repository
    └──

    templates/parts
    │
    ├── components/                          # components that take values
    ├── layout/                              # header, footer, head, .etc
    └──


    functions
    │
    ├── helpers.php                          # helpers php functions
    ├── twig-php-functions.php               # register php functions for twig usage
    └──
