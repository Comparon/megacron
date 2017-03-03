# Comparon Scheduling Bundle

This bundle is designed around the idea to schedule the commands within the project and therefore under VCS control.

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
            "url": "https://github.com/Comparon/ComparonSchedulingBundle.git"
        }
    ]
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

4. Add in config.yml the name of table for history entries:

```yml

megacron:
    history_table: 'history_table_name'

```

By default, the value is 'megacron_history'. If you don't want use the history_table, set history_table to null.

MegaCron store for every executing Command the name, the start and the end time. 
If the command is broken, the end time will be NULL.

5. Update the database structure by doctrine.

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
            ->persistHistory()
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
