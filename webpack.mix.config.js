require("dotenv").config({ path: "../../../.env" });

exports.config = {
  publicPath: "public",
  resourcesPath: "resources",
  blocksPath: "blocks",
  templatesPath: "templates",
  devUrl: process.env.WP_SITEURL,
  extractLibs: ["jquery"],
};


