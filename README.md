# JEXUpdate

Joomla! Extension Update Server with GitHub integration. 

This application allows you to run easily your own Joomla Extension Update Server using GitHub as a repository. All 
extension packages will be stored as an asset in a GitHub repository release. 

## Installation

First you need to get your own GitHub Personal Token from `Seetings > Developer Settings > Personal access tokens`.
No special permissions are required for this.

Running the application is quite easy, you only need to execute a docker container using `-e` option to configure the
list of extensions you want to serve:

```bash
docker pull othercode/jexupdate:latest
docker run -d -p 8080:80 othercode/jexupdate -e GITHUB_TOKEN={token} -e GITHUB_ACCOUNT={account} -e JEX_SERVER_EXTENSIONS={ext_one,ext_two}
```

for example:

```bash
docker run -d -p 8080:80 othercode/jexupdate -e GITHUB_TOKEN=98b669cdb87d168b62ba03fd09dd0e52dbcb0db6 -e GITHUB_ACCOUNT=othercodes -e JEX_SERVER_EXTENSIONS=mod_simplecontactform
```

Here is the complete list of supported environment variables:

| Variable | Description | Default Value |
|----------|-------------|---------------|
| APP_NAME | The application name. | JEXServer |
| APP_DEBUG | Enable disable the debug mode. | `false` |
| APP_ENV | Sets the environment mode | `production` |
| DISPLAY_ERROR_DETAILS | Display the errors. | `false` |
| JEX_SERVER_NAME | The update server name. | JEXServer |
| JEX_SERVER_DESCRIPTION  | The update server description. | "Joomla Extension Update Server" |
| JEX_SERVER_EXTENSIONS | The coma separated list of extensions. |  |
| GITHUB_URI | The GitHub API endpoint. | https://api.github.com/ |
| GITHUB_TOKEN | The GitHub Personal access token. |  |
| GITHUB_ACCOUNT | The GitHub account that holds the extensions repositories. |  |
