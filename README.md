# Data collector for Hetzner

This stand-alone application collects information from Hetzner projects in order to automatically synchronize the farms and virtual machines in iTop.

iTop is a web based open source tool for IT service management tasks by [Combodo](https://github.com/itomig-de/iTop)

 | Name        | Data collector for Hetzner           | 
  | ----        | --------------------------           | 
 | Author      | Itomig GmbH, Lucie BECHTOLD          | 
 | Description | Inventory Data Collector for Hetzner | 
 | Version     | 1.0.0                                | 
 | Release     | 2019-07-18                           | 
 | Diffusion   | iTop Hub, Combodo site               | 
 | Code        | collector.hetzner.itomig             | 
 | Standalone  | yes                                  | 

## Features

Main functions:

* Automatic creation and update of Farms and Virtual Machines in iTop based on Hetzner data.
Technical aspects:
* The collector can reside on any system with web access to iTop and (web) access to the Hetzner API
   * The definition of the mapping between Hetzner fields and iTop fields is partially configurable.
   * The creation of the Synchronization Data Sources in iTop is fully automated.
This collector makes use of iTop's built-in Data Synchronization mechanism. For more information about how the data synchronization works, refer to Data Synchronization Overview 

## Revision History

 | Version | Release Date | Comments        | 
  | ------- | ------------ | --------        | 
 | 1.0.0   | 2019-18-07   | Initial Version | 

## Limitations

   * The current version is synchronizing neither the OS Family nor the OS Version.

## Requirements

   * PHP (command line interface), version 5.3.0 up to 7.2 with the php-curl, php-dom and php-simplexml extensions installed.
   * A minimum of one Hetzner access token (one token per project)
   * An HTTP/HTTPS access to the iTop web services (REST + synchro_import.php and synchro_exec.php)

## Installation

   * Copy “itomig-hetzner-collector” in a folder on the machine that will run the collector application. This machine must have web access to the Hetzner API and a web access to the iTop server.
   * create the file conf/params.local.xml to suit your installation, supplying the tokens to connect to the Hetzner API and iTop.
By default this file should contain the values used to connect to the Hetzner API and to the iTop server:

```
<itop_url>https://localhost/itop/</itop_url>
<itop_login>admin</itop_login>
<itop_password>PASSWORD</itop_password>
<hetzner_tokens type="array">
   <token>Tokkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk</token>
</hetzner_tokens>
<teemip>false</teemip>
```

 | Parameter      | Meaning                                                                                                                                                                      | Sample value              | 
 | ---------      | -------                                                                                                                                                                      | ------------              | 
 | itop_url       | to the iTop Application                                                                                                                                                      | https://localhost/        | 
 | itop_login     | Login (user account) for connecting to iTop. Must have rights for executing the data synchro, to create Persons and Users (and connect to REST services on iTop above 2.5.0) | admin                     | 
 | itop_password  | Password for the iTop account.                                                                                                                                               |                           | 
 | hetzner_tokens | The list of project tokens                                                                                                                                                   |                           | 
 | token          | The Hetzner token to access one project                                                                                                                                      |                           | 
 | teemip         | If iTop is using TeemIp, the managementip field should not be synchronized. Use „true“ if you are using TeemIp, „false“ if your aren‘t                             | `<teemip>`true`</teemip>` | 

Starting with iTop version 2.5.0, the account used to connect to iTop must have the profile REST Services user in order to be allowed to use the web services.
Before iTop version 2.5.0, only Administrators users can create Users.

## Configuration

The default values for the configuration of the data collection is defined in the file collectors/params.distrib.xml. This configuration defines which tokens will be used to retrieve the data, how to map the Hetzner fields with the iTop fields and some default values for the iTop fields.
The file params.distrib.xml contains the default values for the parameters, it's the reference and should remain unmodified.
The file params.local.xml contains the values you have defined. They have precedence over the default values defined in params.distrib.xml
Both files use exactly the same format.
The configuration looks as follows:

 
      <!-- Default Values for synchronized objects -->
      <org>Demo</org>
  
      <!-- Map the hetzner status to the status in itop -->
      <status type="array">
          <pattern>/running/production</pattern>
          <pattern>/initializing/implementation</pattern>
          <pattern>/starting/implementation</pattern>
          <pattern>/stopping/implementation</pattern>
          <pattern>/off/stock</pattern>
          <pattern>/deleting/obsolete</pattern>
          <pattern>/migrating/implementation</pattern>
          <pattern>/rebuilding/implementation</pattern>
          <pattern>/unknown/stock</pattern>
          <!-- <pattern>/.*/%1$s</pattern> -->  
      </status>
      <default_status>stock</default_status>
  
      <!-- Mapping for the OS Family -->
      <os_family type="array">
          <pattern>/ubuntu/Ubuntu</pattern>
          <pattern>/centos/CentOS</pattern>
          <pattern>/debian/Debian</pattern>
          <pattern>/fedora/Fedora</pattern>
          <pattern>/unknown/Unknown</pattern>
          <!-- <pattern>/.*/%1$s</pattern> -->
      </os_family>

 | Parameter | Meaning                                                                                           | Default value                   | 
 | --------- | -------                                                                                           | -------------                   | 
 | org       | The organization for the Farms and Virtual Machines                                               | `<org>`Demo`</org>`             | 
 | status    | The list of Hetzner statuses for the Virtual Machine object and their corresponding iTop statuses | `<code>``<status type="array">`
    `<pattern>`/running/production`</pattern>`
`</status>``</code>`|
 | default_status | The default status in case the status given is not listed in the status mapping | `<default_status>`stock`</default_status>`                                                      | 
 | -------------- | ------------------------------------------------------------------------------- | ------------------------------------------                                                      | 
 | -------------- | ------------------------------------------------------------------------------- | --------------------------------------                                                          | 
 | os_family      | The list of Hetzner OS families and their corresponding names in iTop           | Note : this has to be configurated in iTop too. This collector does not yet collect OS families |  | 

Those parameters can be redefined in the file conf/params.local.xml in order to take into account your specific needs. (For instance the mapping between iTop and Hetzner statuses)
The expected value for org is an organization name, not an id.
The expected value for default_status is its name, not its id.
The collector does not yet collect OS Families and Versions, they have to be manually created in iTop. (a missing OS Version will not prevent the Virtual Machine‘s import)

## Troubleshooting

You can test your configuration without importing any data in iTop by running the following command from the command line:

`php exec.php --console_log_level=9 --collect_only`{bash}

This produces an output similar to the one shown below:


```
   Debug - OK, the required PHP version to run this application is 5.3.0. The current PHP version is 7.3.6-1+ubuntu18.04.1+deb.sury.org+1.
   Debug - OK, the required extension 'simplexml' is installed (current version: 7.3.6-1+ubuntu18.04.1+deb.sury.org+1 >= 0.1).
   Debug - OK, the required extension 'dom' is installed (current version: 20031129 >= 1.0).
   Debug - The following configuration files were loaded (in this order):
   
          - /var/www/html/hetzner-collector/conf/params.distrib.xml
          - /var/www/html/hetzner-collector/conf/params.local.xml
  
   The resulting configuration is:
  
  `<?xml version="1.0" encoding="UTF-8"?>`
  `<parameters>`
    `<itop_url>`http://localhost/itop`</itop_url>`
    `<itop_login>`admin`</itop_login>`
    `<itop_password>``</itop_password>`
    `<console_log_level>`4`</console_log_level>`
    `<syslog_log_level>`-1`</syslog_log_level>`
    `<max_chunk_size>`1000`</max_chunk_size>`
    `<itop_synchro_timeout>`600`</itop_synchro_timeout>`
    `<stop_on_synchro_error>`no`</stop_on_synchro_error>`
    `<curl_options>`
      `<CURLOPT_SSLVERSION>`CURL_SSLVERSION_TLSv1_2`</CURLOPT_SSLVERSION>`
      `<CURLOPT_SSL_VERIFYHOST>`2`</CURLOPT_SSL_VERIFYHOST>`
      `<CURLOPT_SSL_VERIFYPEER>`1`</CURLOPT_SSL_VERIFYPEER>`
    `</curl_options>`
    `<hetzner_tokens type="array">`
      `<item>`kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk`</item>`
    `</hetzner_tokens>`
    `<org>`Demo`</org>`
    `<status type="array">`
      `<item>`/running/production`</item>`
      `<item>`/initializing/implementation`</item>`
      `<item>`/starting/implementation`</item>`
      `<item>`/stopping/implementation`</item>`
      `<item>`/off/stock`</item>`
      `<item>`/deleting/obsolete`</item>`
      `<item>`/migrating/implementation`</item>`
      `<item>`/rebuilding/implementation`</item>`
      `<item>`/unknown/stock`</item>`
    `</status>`
    `<default_status>`stock`</default_status>`
    `<os_family type="array">`
      `<item>`/linux/Linux`</item>`
      `<item>`/centos/CentOS`</item>`
      `<item>`/debian/Debian`</item>`
      `<item>`/fedora/Fedora`</item>`
      `<item>`/unknown/Unknown`</item>`
    `</os_family>`
  `</parameters>`
  
  Debug - Registered collectors:
  Debug - Collector: iTopFarmCollector, version: 1.0.0
  Debug - Collector: iTopVirtualMachineCollector, version: 1.0.0
```

You can see the order in which the configuration files were loaded and the resulting configuration.

## Usage

To launch the data collection and synchronization with iTop, run the following command (from the root directory where the data collector application is installed):
php exec.php
The following (optional) command line options are available:

 | Option                        | Meaning                                                                                                                                                    | default value | 
 | ------                        | -------                                                                                                                                                    | ------------- | 
 | --console_log_level=`<level>` | Level of output to the console. From -1 (none) to 9 (debug).                                                                                               | 6 (info)      | 
 | --collect_only                | Run only the data collection, but do not synchronize the data with iTop                                                                                    | false         | 
 | --synchro_only                | Synchronizes the data previously collected (stored in the data directory) with iTop. Do not run the collection.                                            | false         | 
 | --configure_only              | Check (and update if necessary) the synchronization data sources in iTop and exit. Do NOT run the collection or the synchronization                        |               | 
 | --max_chunk_size=`<size>`     | Maximum number of items to process in one pass, for preserving the memory of the system. If there are more items to process, the application will iterate. | 1000          | 

The execution of the command line will:
    1. Connect to iTop to create the Synchronization Data Sources (or check their definition if they already exist, updating them if needed)
    2. Connect to the Hetzner API to collect the information about the Farms and the Virtual Machines
    3. Upload the collected data into iTop
    4. Synchronize the collected data with the existing iTop Farms and Virtual Machines.
When the collector is run, two Synchro Data Sources are created and used for synchronizing Farms and and Virtual Machines objects in iTop.

## Scheduling

Once you've run the data collector interactively, the next step is to schedule its execution so that the collection and import occurs automatically at regular intervals.
The data collector does not provide any specific scheduling mechanism, but the simple command line php exec.php can be scheduled with either cron (on Linux systems) or using the Task Scheduler on Windows.
For optimal results, don't forget to adjust the configuration parameter full_load_interval to make it consistent with the frequency of the scheduling.
