#!/bin/python3
# coding: utf-8
import os
from datetime import date

fechaActual = date.today()
cadenaSemestre = "10" if fechaActual.month <= 6 else "20"

# No es como tal constante pero no se modifica en todo el programa
CICLO_ACTUAL = f"{fechaActual.year}{cadenaSemestre}"

# Datos necesarios para obtener la oferta
SERVIDOR_SIIAU = 'http://consulta.siiau.udg.mx'
APP_SIIAU = f'{SERVIDOR_SIIAU}/wco/sspseca.consulta_oferta'
CENTRO_DEF = "D"  # CUCEI
PLAN_DEF = "ICOM"  # Computación, técnicamente no es necesario pero de todos modos lo agrego
# Número de clases a mostrar, ponemos un valor exagerado para asegurarnos que nos muestre todos
CANT_RESULT_DEF = 10000  # Índice clases

RAIZ_WEB = os.path.dirname(os.path.dirname(__file__))
CARPETA_DATOS = os.path.join(RAIZ_WEB, "datos")
ARCHIVO_PLANES = os.path.join(CARPETA_DATOS, "planes.xml")
