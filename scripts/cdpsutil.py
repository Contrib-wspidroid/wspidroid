#!/usr/bin/env python
# -*- coding: UTF-8 -*-

# Import libraries Python
import sys
import psutil

# Execution de la commande passee en argument.
try:
  valRetour = eval(sys.argv[1])
except:
  valRetour = "erreur"

# On retour la valeur.
print (valRetour)
