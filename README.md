>Read this document in another language: [Russian / русский](/README.ru.md)

# Wordpress plugin "MX Individual CSS/JS"

As a rule you write CSS/JS for different pages (for homepage, single page, product page, etc.) in one stylesheet or JS file. When your project grows up and amount of CSS/JS code becomes huge, it's getting more and more palpaple, that a big number of CSS/JS code is not relevant to the every certain page. Because of this excess code the size of the individual site's pages is bigger than it could be.

It would be useful if we could write different CSS/JS for individual types of pages. This plugin makes it easy. For example, just create single.css and this plugin will automatically append this css to every single page of the site. If you want to write CSS for Woocommerce product page, just add single-product.css. You don't need any extra actions but create CSS/JS for the type of pages you want. This plugin automatically checks for existent CSS/JS files, which relevant to the current page and append them to the page.

## The file's naming rules for automatically appending

The naming rules for CSS/JS files is similar to the naming rules for the theme pages:

  - *admin.css / admin.js* — append to every admin page
  - *home.css / home.js* — append to the home page
  - *search.css / search.js* — append to the search page
  - *404.css / 404.js* — append to the 404 error page
  - *singular.css / singular.js* — append to every singular page (single page or single post)
  - *single.css / single.js* — append to every single post (of any type, not only built-in "post" type)
  - *single-{posttype}.css / single-{posttype}.js* — append to every single post of the certain post type "{posttype}"
  - *page.css / page.js* — append to every single page
  - *page-{id}.css / page-{id}.js* — append to the certain page with ID {id}
  - *page-{slug}.css / page-{slug}.js* — append to the certain page with slug {slug}
  - *archive.css / archive.js* — append to every archive page
  - *category.css / category.js* — append to every category page
  - *category-{id}.css / category-{id}.js* — append to the category page with certain ID {id}
  - *category-{slug}.css / category-{slug}.js* — append to the category page with certain slug {slug}

CSS/JS files should be placed in directories which defined on this plugin's settings page. ***By default the directories for CSS and JS files are subdirectories "css" and "js" in the theme directory***.

>Another benefit of such distribution of CSS/JS code among different files is that it allow to organize your code in logical parts. So, you can easily find the part of code related to the certain page, which you need to modify. The more bigger the size of you project the more obvious this benefit.

## Support of minified versions of CSS/JS files

This plugin also detects minified versions of CSS/JS files (.min.css and .min.js) and appends it instead of non-minified files.

## Installation

  1. Download the [latest](https://github.com/mx-studio/mx-individual-css-js/releases/latest) zip of this repo
  2. In your WordPress admin panel, navigate to Plugins->Add New
  3. Click "Upload Plugin"
  4. Upload the zip file that you downloaded
  5. Activate plugin

## License
The project is licensed under the GPLv3 or later