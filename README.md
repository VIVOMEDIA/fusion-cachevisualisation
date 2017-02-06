# Fusion Cache Visualisation
Helps to debug cache configurations in [Neos Fusion](https://github.com/neos/typoscript/).

This is just a debugging tool and highly recommended to use in development context only.

## What it provides
This plugin adds a wrapper around all cached and uncached segments to visualise them. Also it provides additional information for each segement:

Cached Segments
* Fusion path
* Cache tags
* Lifetime

Uncached Segments
* Fusion path
* Context variable names

# Install
## Composer
Install via composer as a dev package
```bash
php composer.phar require --dev "vivomedia/fusion-cachevisualisation" "^1.0"
```

## Flow
After install clear the content cache of your flow/neos application.
```bash
./flow flow:cache:flush --force
```

## jQuery
The plugin uses jQuery. Please ensure that it's loaded early enough.

# Configuration
You can enable and disable the plugin within your `Settings.yaml`

```yaml
VIVOMEDIA:
  Fusion:
    CacheVisualisation:
      enabled: true # Or false
```

It is enabled by default for the development context.

Please keep in mind to clear the cache after enabling/disabling the plugin.

# Screenshots
![Cached segment](/Docs/screenshot_cached.png?raw=true "Cached segment")
![Unached segment](/Docs/screenshot_uncached.png?raw=true "Uncached segment")
