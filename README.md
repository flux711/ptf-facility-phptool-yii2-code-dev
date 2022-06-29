# Rhea facility data extension

This repository handles the facility data which can be used to simulate ....

## Prerequisites

* Installed version of Rhea: https://github.com/phytec/ptf-rhea-dev
* Access to the defined database in Rhea

## Installing

Add into the **require** section of your composer.json file the following
string: ```"flux711/facility-phptool-code-dev": "dev-master" ```and update
composer: ```sudo docker exec -it --user www-data rhea_web_1 composer update -d rhea-yii2```

Go to your application config file inside the module section (e.g. rhea-yii2/api/config/main-local.php) and add the
following to your config to connect the project to your module:

```
$config['bootstrap'][] = 'facility';
$config['modules']['facility'] = [
  'class' => 'flux711\yii2\facility_phptool_code_dev\Module',
  // uncomment the following to add your IP if you are not connecting from localhost.
  //'allowedIPs' => ['127.0.0.1', '::1'],
];
```

## Usage

Your Rhea instance should now contain a facility section which can be operated by a privileged user.
