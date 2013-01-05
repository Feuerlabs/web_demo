(C) 2013 Feuerlabs, Inc.

Magnus Feuer.

Installing the Feuerlabs web demo.

# BACKGROUND

The web demo is a proof of concept for a basic web interface to provision
and manage devices in an exosense server. The web demo uses the following technologies.


- php5<br>
Core language usage.
<br>
- php5-gd<br>
Graphics library used by libchart, see below
<br>
- postgres 8.4 (or later)<br>
SQL database used for local storage
<br>
- php5-pgsql<br>
Postgres integration from php
<br>
- apache<br>
Web server
<br>
- Codeigniter 2.1.3 (Provided by webdemo)<br>
http://ellislab.com/codeigniter
Simple webapp framework on top of PHP.
<br>
- CodeIgniter Google Maps V3 API Library 2012-12-27 (no version tag)<br>
http://biostall.com/codeigniter-google-maps-v3-api-library
Integrates code igniter with Google maps. Used for waypoint plotting
<br>
- libchart 1.3<br>
http://naku.dohcrew.com/libchart/pages/introduction/
Used to render line charts for log data. Patched in very minor way by Feuerlabs
<br>
- Code Igniter JSON-RPC 1.0<br>
http://ellislab.com/forums/viewthread/98953/
Modified by Feuerlabs to use php-curl in order to get https support.

# INSTALL INSTRUCTIONS

## [Prepare] Become root

    sudo su -

## [Prepare] Install the prerequisite packages

    apt-get install php5 php5-curl php5-pgsql php5-gd postgresql-8.4 curl

Additional packages and/or different versions may be needed.

## [Prepare] Unpack the web demo on the target host

This README.md file should be in the unpacked directory,
which will be called **$UNPACK** from now on

## [Prepare] Create a system folder for php files,
This folder should be outside the web root and should be owned
by whoever is running apache.

A suggestion is

    /var/local/fl_demo

This direcotry is called **$SYSFOLD** from now on


## [Prepare] Clean out webroot

This is the top directory where apache will serve pages from. It is usually set to

    /var/www

This directory is called **$WEBROOT** from now on.

After checking that $WEBROOT does not contain anything worth to be saved, remove all files in that director with:

    rm -rf $WEBROOT/*


## [Prepare] Create $WEBROOT/generated
This directory will host the graphs displayed when detailed views of a device log is selected.

    mkdir $WEBROOT/generated


## [DB] Create a database user for the web demo

### Sudo into postgres user.

    su -s /bin/sh postgres

### Start postgres psql client

    psql

### In psql, create a codeigniter user

    create user codeigniter with password 'exosense_demo';

Change the password to something smarter.


### In psql, create a codeigniter database

    CREATE DATABASE codeigniter
      WITH OWNER = codeigniter
        ENCODING = 'UTF8'
        TABLESPACE = pg_default
        LC_COLLATE = 'C'
        LC_CTYPE = 'C'
        CONNECTION LIMIT = -1;

If postgres barfs on UTF8 as a charset, try SQL_ASCII instead.


### Exit the psql client

To exit psql, type:

    \q

### Exit the postgress shell

    exit

You should now be back as root.


### Start psql as database user codeigniter.

From the $UNPACK direcotry (as root), start psql with the database user codeigniter

    cd $UNPACK
    psql -h localhost -U codeigniter

Use the password you assigned when you created the codeigniter user.


### In psql, create tables, indexes, and the rest
Read and execute web_demo.sql with the following command.

    \i web_demo.sql

Minor bitching about extensions and public grants are ok.


## [Install] Unpack codeigniter into $SYSFOLD

    cd $SYSFOLD
    tar xf $UNPACK/CodeIgniter_2.1.3.tgz


## [Install] Copy $UNPACK/to_sysfolder to $SYSFOLD

    cd $SYSFOLD
    cp -r $UNPACK/to_sysfolder/* .

## [Install] Edit $SYSFOLD/index.php

Change

    $system_path = 'system';

to

    $system_path = '/var/local/fl_demo/system';

Change

    $application_folder = 'application';

to

    $application_folder = '/var/local/fl_demo/application';

In both cases, replace /var/local/fl_demo with your $SYSFOLD value.


## [Install] Move $SYSFOLD/index.php and $SYSFOLD/fldemo.css to $WEBROOT

    cd $SYSFOLD
    mv index.php fldemo.css $WEBROOT


## [Install] Unpack the google maps api into $SYSFOLD

    cd $SYSFOLD
    tar xf $UNPACK/codeigniter_google_maps_api-2012-12-27.tgz


## [Install] Unpack the libchart library into $SYSFOLD

    cd $SYSFOLD
    tar xf $UNPACK/libchart-1.3.fl_patched.tgz


## [Install] Edit $UNPACK/config/config.php


Change the following entry to refer to the hostname that the demo is accessed as

    $config['base_url'] = 'http://localhost/';

Change the following entry to the root of the unpacked libchart library

    $config['libchart_root'] = '/var/local/fl_demo/libchart';


## [Install] Edit $UNPACK/config/database.php

Change the following three entries to reflect the postgres user, password and database setup above:

    $db['default']['username'] = 'codeigniter';
    $db['default']['password'] = 'exosense_demo';
    $db['default']['database'] = 'codeigniter';


## [Install] Edit $UNPACK/config/exosense.php

Change the following entry to point to the URL where the exosense server can be reached:

    $config['exosense_url'] = 'https://vps.ulf.wiger.net:8088/ck3/rpc';

Change the following entry to provide the basic HTTP authentication user name:

    $config['exosense_user'] = 'exosense_user';

Change the following entry to provide the basic HTTP authentication password:

    $config['exosense_password'] = 'exosense_password';


The three remaining configuraition lines:

    $config['exosense_yang_file'] = 'demo.yang';
    $config['exosense_notification_url'] = 'http://localhost:9123';
    $config['exosense_timeout'] = 30;

can be left as is.

Please note that $UNPACK/config/routes.php can be left as is

## [Install] Copy the configuration files into the $SYSFOLD structure

    cp $UNPACK/config/*.php $SYSFOLD/application/config

## [Install] Set the correct owner on $WEBROOT and $SYSFOLD

Find out what user Apache runs as, and which default group that user has.
Do recursive ownership change of all files in $WEBROOT and $SYSFOLD

    chown -R www-user.www-group $WEBROOT $SYSFOLD

Replace the www-user and www-group above with the apache runtime user and group.

## [Exosense] Upload Demo.yang to the exosense server

Set and export the EXOSENSE_URL environment variable to point to the
exosense server. This value is the same as $config['exosense_url'] in
the exosense.php configuration file. An example would be

    export EXOSENSE_URL=https://vps.ulf.wiger.net:8088/ck3/rpc

Set and export the EXOSENSE_AUTH environment variable to provide basic HTTP authentication credentials.
These are also set to the same values as in the exosense.php configuration file.  An example would be

    export EXOSENSE_AUTH=exosense_user:exosense_password

Run the create yang script (which invokes curl) to upload $UNPACK/demo.yang to the exosense server.

    cd $UNPACK/script
    sh create-yang-module.sh ../demo.yang


# Populating test data
If a device is not available to generate alarms, logs and waypoints,
scripts are available to create these items. The following instructions show how to provision simulated devices and populate them with test data.

## Create CAN frames specifications

Surf to the web server and select the "CAN Frames" tab.
Add a number of CAN frames with a numeric Can Frame ID in the 1-1000 range, a suitable label, unit of measurement, descriptoin and numeric min/max values between 1 and 32767

Repeat the process with two additional frames, each with their own unique Can Frame ID.


## Add a device

Surf to the web server and select the "Devices" tab.

Click on Add Device and setup a device with the following fields:

- DeviceID<br>
A string between 1 and 64 characters.
<br>
- DeviceType<br>
A drop down meny with all available device types provisioned in the Exosense Server
<br>
- Description<br>
A text decribing the device.
<br>
- Server Key
The Server side authentication key. Numeric 64bit int
<br>
- Device Key
The Server side authentication key. Numeric 64bit int
<br>
- Waypoint Interval
The interval, in meters, between each logged waypoint.
<br>
- CAN Bus Speed
The baudrate of the CAN Bus of the device.
<br>
- CAN frame ID size
The number of bits that the Frame ID occupies in each CAN message.
<br>
- Retry count
The number of times that the device shall attempt to contact the
server, should the latter not be available due to out-of-coverage
situations.
<br>
- Retry Interval
The number of seconds to wait between each connection retry.


## Setup logging for device

Surf to the web server and select the "Devices" tab.

Click on "setup logging" for the newly created device.

Click on "Add Log Specification". Select one of the provisioned CAN
frames, enter the number of milliseconds between each CAN sample, and
the number of CAN frames to store on the device (in a circular buffer)
pending transmission to the server.

Add a second Log specicication using another CAN Frame than the first one.

Click Push Log Specification to transmit the complete logging
specification to the exosense server for further transmission to the
(non-existent) device. This operation will fail silently when it times out
without the device calling in to receive the update.


## Setup alarms for device

Surf to the web server and select the "Devices" tab.

Click on "setup alarm" for the device created above.

Click on "Add Alarm Specification". Select one of the provisioned CAN
frames and enter the trigger and reset thresholds as integers. Click Create .

Add a second Alarm specicication using another CAN Frame than the first one.

Click Push Alarm Specification to transmit the complete alarm
specification to the exosense server for further transmission to the
(non-existent) device. This operation will fail silently when it times out
without the device calling in to receive the update.

## Generate log data

Set the URL environment variable to point to the exosense JSON-RPC address managed by the web demo server. The hostname is the same as specified in config.php above. The remainder of the path is to be set to
/index.php/exosense. A valid example for the URL variable would be:

    export URL=http://vps.ulf.wiger.net/index.php/exosense

If basic http authentication is used to access the web demo, the AUTH environment variable should be setup with the necessary credentials:

    export AUTH=demo:exosense_demo

If AUTH is not set, the scripts will acces the web demo without any credentials.

    cd $UNPACK/script
    sh log.sh <device-id> <can-frame-id> <min-val> <max-val> <nr_elem>

- <device-id><br>
Is the ID of the device created above.
<br>
- <can-frame-id><br>
Is set to one of the CAN frame IDs given when the log specification for the device was setup
<br>
- <min-val><br>
The minimum value for the CAN frame that should be generated.
<br>
- <max-val><br>
The maximum value for the CAN frame that should be generated
<br>
- <nr_elem>
The number of CAN frames that should be generated in the log.

The logged CAN frames will start at <min-val> and be incremented by one for each frame logged until <max-val> is reached. At that point the value is reset to <min-frame> and the process starts over until the given number of frames have been logged.


## Generate alarms

Ensure that the URL environment variable is setup as described above.

Generate alarms for the created device:

    cd $UNPACK/script
    sh log.sh <device-id> <can-frame-id> <can-value>

- <device-id><br>
Is the ID of the device created above.
<br>
- <can-frame-id><br>
Is set to one of the CAN frame IDs given when the alarm specification for the device was setup
<br>
- <can-value><br>
The value that triggered the alarm. Should be between the trigger and
reset thresholds specified for the given alarm.

Several alarms can be queued for a single can frame on a given device.


## Generate waypoints

Ensure that the URL environment variable is setup as described above.

Generate waypoints for the created device:

    cd $UNPACK/script
    sh waypoints.sh <device-id> <waypoint-feed-file>

- <device-id><br>
Is the ID of the device created above.
<br>
- <waypoint-feed-file><br>
A file with timestams, latitude and longitude waypoint records.

Each waypoint record in the feed file is separated by a newline and has the following format:

    { "ts": "<timestamp>", "lat": "<latidute>", "lon": "<longitude>" }

- <timestamps>
A UTC timestamp with the number of seconds since the 1970-01-01 00:00:00.
<br>
- <latitude>
A decimal latitude.
<br>
- <longitude>
A decimal longitude.

An example of a waypoint record is given below:

    { "ts": "1356042587", "lat":"56.633995", "lon":"16.463807" }


# Logs

    /var/log/apache2/access.log
    /var/log/apache2/error.log
    $SYSFOLD/application/logs/log-YYYY-MM-DD.php

