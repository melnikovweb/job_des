const mix = require("laravel-mix");
const sassGlobImporter = require("node-sass-glob-importer");
const FriendlyErrorsPlugin = require("friendly-errors-webpack-plugin");
const notifier = require("node-notifier");
const { config } = require("./webpack.mix.config");
const CopyPlugin = require("copy-webpack-plugin");

require("laravel-mix-assets-splitter");
require("laravel-mix-php-manifest");
require("laravel-mix-stylelint");
require("laravel-mix-eslint");
require('laravel-mix-simple-output');

const sassConfig = {
  sassOptions: {
    importer: sassGlobImporter(),
  },
};

mix
  .setPublicPath(config.publicPath)
  .js(config.resourcesPath + "/js/app.js", config.publicPath + "/js")
  .sass(
    config.resourcesPath + "/scss/app.scss",
    config.publicPath + "/css",
    sassConfig
  )
  .split(
    "js",
    config.templatesPath,
    config.publicPath + "/js/" + config.templatesPath
  )
  .split(
    "scss",
    config.templatesPath,
    config.publicPath + "/css/" + config.templatesPath,
    sassConfig
  )
  .split(
    "scss",
    config.blocksPath,
    config.publicPath + "/css/" + config.blocksPath,
    sassConfig
  )
  .split(
    "js",
    config.blocksPath,
    config.publicPath + "/js/" + config.blocksPath
  )
  .split(
    "js",
    config.resourcesPath + "/js/wp-dashboard",
    config.publicPath + "/js/wp-dashboard"
  )
  .split(
    "scss",
    config.resourcesPath + "/scss/wp-dashboard",
    config.publicPath + "/css/wp-dashboard",
    sassConfig
  )
  .browserSync({
    proxy: config.devUrl,
    watchEvents: ["add", "change", "unlink", "addDir", "unlinkDir"],
    files: [
      "public/**/*.css",
      "public/**/*.js",
      "functions.php",
      "includes/**/*.php",
      "templates/**/*.php",
      "blocks/**/*.php",
      "templates/**/*.twig",
      "blocks/**/*.twig",
      {
        match: [
          "templates/**/*.scss",
          "blocks/**/*.scss",
          "templates/**/*.js",
          "blocks/**/*.js",
        ],
        fn: function (event, file) {
          console.log(event, " - ", file);
        },
      },
    ],
  })
  .options({
    processCssUrls: false,
    runtimeChunkPath: "js",
    sourceMap: true,
  })
  .stylelint({
    configFile: ".stylelintrc",
    context: ".",
    files: "**/*.scss",
    failOnWarning: true,
    failOnError: true,
  })
  .eslint({
    extensions: ["js"],
    failOnWarning: true,
    failOnError: true,
  })
  .phpManifest()
  .disableNotifications()
  .simpleOutput()
  .webpackConfig({
    stats: false,
    devtool: 'cheap-module-source-map',
    optimization: {
      emitOnErrors: false,
    },
    devServer: {
      allowedHosts: 'all',
      bonjour: true,
    },
    plugins: [
      new FriendlyErrorsPlugin({
        compilationSuccessInfo: {
          messages: [],
          notes: [],
        },
        onErrors: function (severity, errors) {
          if (severity !== "error") {
            return;
          }
          const error = errors[0];
          notifier.notify({
            message: error.name,
            title: severity + " : " + error.name,
          });
        },
        clearConsole: true,
        additionalFormatters: [],
        additionalTransformers: [],
      }),
      new CopyPlugin({
        patterns: [
          {
            from: config.resourcesPath + "/fonts",
            to: "fonts",
            noErrorOnMissing: true,
          },
          {
            from: config.resourcesPath + "/img",
            to: "img",
            noErrorOnMissing: true,
          },
        ],
      }),
    ],
  });

config.extractLibs.forEach((lib) => {
  mix.extract([lib], "/js/vendor/" + lib);
});  


//production pipes
if (mix.inProduction()) {
  mix.sourceMaps();
  mix.version();
}
