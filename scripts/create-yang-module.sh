#!/bin/sh


if [ $# != 1 ]
then
    echo "Usage: $0 yang-file"
    exit 255
fi

if [ ! -f $1 ]
then
    echo "File $1 is not readable."
    exit 255
fi


if [ "$EXOSENSE_URL" == "" ]
then
    echo "Please set the EXOSENSE_URL environment variable."
    exit 255
fi

if [ "$EXOSENSE_AUTH" == "" ]
then
    echo "Please set the EXOSENSE_AITH environment variable."
    exit 255
fi


sed 's/"/\\"/g' < $1 > /tmp/create_yang_module.tmp
curl -u $EXOSENSE_AUTH -k -X POST $EXOSENSE_URL --data-binary @- << EOF
{
    "jsonrpc": "2.0",
    "method": "exodm:create-yang-module",
    "id": "1",
    "params":
    {
        "name": "$1",
        "repository": "user",
        "yang-module": "$(cat /tmp/create_yang_module.tmp)"
    }
}
EOF
