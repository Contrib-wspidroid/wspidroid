#!/bin/bash
BTN=$1
STATUS=$2

BTN=$(echo $BTN | tr '[:lower:]' '[:upper:]')
STATUS=$(echo $STATUS | tr '[:lower:]' '[:upper:]')

DIP_SWITCH=""  #Change dip switches to match the remote

#hardcoded values
BTN_A="10100000000000001"
BTN_B="100010101"
BTN_C="101000101"
BTN_D="101010001"

DIP_SWITCH=$(echo $DIP_SWITCH | sed 's/D/10/g' | sed 's/U/00/g')

case $BTN in
    A )
        BTN=$BTN_A ;;
    B )
        BTN=$BTN_B ;;
    C )
        BTN=$BTN_C ;;
    D )
        BTN=$BTN_D ;;
    * )
        echo "Please define the button [A-D]";exit;
esac
case $STATUS in
    ON )
        STATUS="0101" ;;
    OFF )
        STATUS="0100" ;;
    * )
        echo "Please define the button state [ON/OFF]";exit;
esac

BIN=$(echo $DIP_SWITCH$BTN$STATUS)
DEC=$((2#$BIN))

#echo $DEC
sudo ./codesend $DEC
