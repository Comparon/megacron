# Comparon Megacron Bundle

This bundle is designed around the idea to schedule the commands within the project and therefore under VCS control.

## IMPORTANT

Use this version with at least symfony 4

## Installation

1. Add the bundle to your project as a composer dependency:
```javascript
// composer.json
{
    // ...
    require: {
        // ...
        "comparon/megacron": "dev-master"
    },
    // ...
    "repositories": [
        // ...
        {
            "type": "vcs",
            "url": "https://github.com/Comparon/Megacron.git"
        }
    ]
}
```

2. Update your composer installation:
```shell
composer update
````

3. Add the bundle to bundles.php:
```php
// config/bundles.php
return  [
        // ...
        Comparon\MegacronBundle\ComparonMegacronBundle::class => [ 'all' => true],
    ];
```
4. Add command to services.yaml

```yaml
    Comparon\MegacronBundle\Command\SchedulerCommand:
        arguments:
          $projectDir: '%kernel.project_dir%'
        tags:
          - 'console.command'
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

    /**
     * @return TaskConfiguration[]
     */
    public function getTaskConfigurations()
    {
        $configs = [];

        $configMonday = new TaskConfiguration();
        $configMonday->setCronExpression('* * * * 1');
        $configs[] = $configMonday;

        $configTuesday = new TaskConfiguration();
        $configTuesday
            ->setCronExpression('0 * * * 2')
            ->setWithOverlapping(false)
        ;
        $configs[] = $configTuesday;

        return $configs;
    }
}
```

### Running your cron jobs automatically

To facilitate this, you can create a cron job on your system like this:
```
* * * * * <path to console> comparon:scheduler:run
```
