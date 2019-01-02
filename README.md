# Crosscut Festival

Source for [https://festival.crosscut.com](https://festival.crosscut.com).

Repo root contains frontend source.

`api` folder contains backend "api" elements.



**Front End**

_Getting Started:_

- Make sure Node.js is installed.
- Install gulp cli globally. `npm install -g gulp-cli`.
- Install dependencies. `npm install`.
- Get vendor files and run build.  `gulp init`.

_Developing with BrowserSync:_

- Run `gulp watch`. This starts a local mini-server, provides a URL to view the site (http://localhost:3000), and will open the URL in a browser automatically. BrowserSync reloads the page automatically when changes are saved.

- Templates: Uses [Nunjucks](https://mozilla.github.io/nunjucks/) templating engine. Individual pages can be found in `./src/pages`. Partials and base template files can be found in `./src/templates`. Variables are stored as JSON in `./src/data`.