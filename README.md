# Project is WIP

# Comparon Scheduling Bundle

This bundle is designed around the idea that you have one command that is triggered by crontab and handles the
scheduling of the other commands ... TODO

## Installation

1. Add the bundle to your project as a composer dependency:
```javascript
// composer.json
{
    // ...
    require: {
        // ...
        "Comparon/SchedulingBundle": "dev-master"
    },
    // ...
    "repositories": [{
        "type": "vcs",
        "url": "https://github.com/Comparon/ComparonSchedulingBundle.git"
    }]
}
```

2. Update your composer installation:
```shell
composer update
````

3. Add the bundle to your application kernel:
```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new Comparon\SchedulingBundle\ComparonSchedulingBundle(),
    ];
    // ...
    return $bundles;
}
```

## Start using the bundle

### Schedule your Command

All you have to do is to implement the TaskInterface (and therefore the method getTaskConfigurations()) in your Command:

```php
class DemoCommand extends ContainerAwareCommand implements TaskInterface
{
    protected function configure()
    {
        // ...
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...
    }
    
    public function getTaskConfigurations()
    {
        TODO
    }
}
```

### Running your cron jobs automatically

To facilitate this, you can create a cron job on your system like this:
```
* * * * * cd /<path to bin folder>; ./console Megacron
```
