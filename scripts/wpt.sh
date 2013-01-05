#!/bin/sh


if [ $# != 2 ]
then
    echo "Usage: $0 device-id waypoint-feed"
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


cat $2 | while read LINE
do
curl $AUTHCMD $AUTH --request POST $URL -d @- <<EOF
{
    "json-rpc": "2.0",
    "method": "process-waypoints",
    "id": "1",
    "params": {
        "device-id": "$1",
	"waypoints": [
           $LINE
	]
    }
}
EOF
echo "Done with $LINE"
done