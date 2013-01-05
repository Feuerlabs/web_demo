#!/bin/sh

if [ $# != 5 ]
then
    echo "Usage: $0 device-id frame_id min_val max_val nr_elem"
    exit 255
fi

if [ "$URL" == "" ]
then
    echo "Please set the URL to the exosense server address."
    echo "Maybe: export URL=http://vps.ulf.wiger.net/index.php/exosense"
    exit 255
fi

AUTH=demo:exosense_demo

dev_id=$1
frame_id=$2
min_val=$3
max_val=$4
nr_elem=$5
cv=$min_val
i=0
while [ $i -lt $nr_elem ]
do
curl -u $AUTH --request POST $URL -d @- <<EOF
{
    "json-rpc": "2.0",
    "method": "process-logdata",
    "id": "1",
    "params": {
        "device-id": "$1",
	"logdata": [
           { "can-frame-id": "$frame_id", "ts": "$(date +%s).$(date +%N)", "can-value": "$cv" }
	]
    }
}
EOF
i=$(($i + 1))
cv=$(($cv + 1))
if [ $cv -gt $max_val ]
then
    cv=$min_val
fi
done