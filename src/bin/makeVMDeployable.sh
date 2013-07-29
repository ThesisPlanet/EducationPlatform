#!/bin/sh
## Delete the persistent MAC address from the persistent rules.
echo "deleting persistent network file."
rm -f /etc/udev/rules.d/70-persistent-net.rules
## remove the HWADDR line.
echo "deleting mac address from configuration file."
awk '!/HWADDR/' /etc/sysconfig/network-scripts/ifcfg-eth0 > /tmp/eth0 && mv /tmp/eth0 /etc/sysconfig/network-scripts/ifcfg-eth0
