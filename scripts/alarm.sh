#!/bin/sh


if [ $# != 3 ]
then
    echo "Usage: $0 device-id frame-id can-value"
    exit 255
fi

if [ "$URL" == "" ]
then
    echo "Please set the URL to the exosense server address."
    echo "Maybe: export URL=http://vps.ulf.wiger.net/index.php/exosense"
    exit 255
fi


if [ "$AUTH" != "" ]
then
    AUTHCMD=-u
fi

dev_id=$1
frame_id=$2
value=$3
curl $AUTHCMD $AUTH --request POST $URL -d @- <<EOF
{
    "json-rpc": "2.0",
    "method": "process-alarms",
    "id": "1",
    "params": {
        "device-id": "$1",
        "alarmdata" : [
          { "can-frame-id": "$2", "ts": "$(date +%s).$(date +%N)", "can-value": "$3" }
        ]
    }
}
EOF
