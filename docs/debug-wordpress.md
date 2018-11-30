# Debug WordPress PHP #

We can debug our source code using Xdebug component, that is already installed in the Docker image. To do so, you need to use an editor/IDE that supports a XDebug plugin. We do recommend [VSCode](https://code.visualstudio.com/Download). It's really great tool and works on Linux, Windows and Mac.

## Configuring ##

You just need to configure the debug in you IDE/Text editor.

If you run into errors regarding the xdebug.idekey you might have to comment out the line in `{projectroot}/wordpress/build/Dockerfile` that sets the `xdebug.idekey`

### VSCode ###

1. Press F1, type *ext install php-debug* and hit enter.
1. After that just, click in the debug icon in the left side bar, then click in the gear, select PHP. Be sure that your launcher.json file has the following content:

```json
{
  "version": "0.2.0",
  "configurations": [

  {
    "name": "Listen for XDebug",
    "type": "php",
    "request": "launch",
    "port": 9000,
    "pathMappings": {
      "/var/www/html/wp-content":"${workspaceRoot}/wordpress/wp-content"
    }
  },
  {
    "name": "Launch currently open script",
    "type": "php",
    "request": "launch",
    "program": "${file}",
    "cwd": "${fileDirname}",
    "port": 9000
  }
  ]
}
```

1. Save the launcher.json. Restart VSCode.

To debug a source code just open a .php file, click before the line number (in the left side) and run some request/url that should execute this php file.

#### If debug does not work ####

- Stop/start the docker container
- Close/open the VSCode/IDE
- Check you are not viewing a cached page.

### PhpStorm ###

PhpStorm already ships with XDebug support as it was designed for php.

1. Press ctrl+alt+s to access the project settings. Go to _Languages & Frameworks_ > _PHP_ > _Debug_ and make sure the debug port is set to `9000` and external connections are allowed.
2. Create a new debug configuration _Run_ > _edit configurations_. Use the _PHP REMOTE DEBUG_ Preset, name the configuration and check the "Filter debug connection by IDE key" to make path mapping available.
3. Set the IDE key to `docker`.
4. Click inside the _Server_ input field and press shift+enter (or use `...`) to create a new server configuration. Give it a name.
5. _Host_ should be `localhost`, _Port_ : `80` and _Debugger_ : `Xdebug`.
6. Check _Use path mappings_ and set the _absolute path on server_ to `/var/www/html/wp-content` for `{workspaceRoot}/wordpress/wp-content` to link your local files with the corresponding server files.

To debug a source code just open a .php file, click before the line number (in the left side) and run some request/url that should execute this php file.