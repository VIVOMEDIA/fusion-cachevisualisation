# TypoScript Cache Visualisation
Helps to debug cache configurations in [Neos TypoScript](https://github.com/neos/typoscript/).

This is just a debugging tool and highly recommended just to use in development context.

## What it provides
This plugin add a wrapper around all cached and uncached segments to visualise them. Also it provides addional informations for each segement:

Cached Segments
* TypoScript path
* Cache tags
* Lifetime

Uncached Segments
* TypoScript path
* Context variable names

# Install
## Composer
Install via composer as a dev package
```bash
php composer.phar require --dev "vivomedia/typoscript-cachevisualisation" "~0.1"
```

## Flow
After install clear the content cache of your flow/neos application.
```bash
./flow flow:cache:flush --force
```

# Configuration
You can enable and disable the plugin within you `settings.yaml`

```yaml
VIVOMEDIA:
  TypoScript:
    CacheVisualisation:
      enabled: TRUE # Or false
```

Is is enabled by default for the development context.

Please keep in mind to clear the cache after enabling/disabling the plugin.

# Screenshots
![Cached segment](/Docs/screenshot_cached.png?raw=true "Cached segment")
![Unached segment](/Docs/screenshot_uncached.png?raw=true "Uncached segment")
