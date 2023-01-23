#!/bin/python3
# coding: utf-8

import re

class Horario:
    horarios = list()
    centro = str()
    sesion = str()
    horas = str()
    dias = str()
    edificio = str()
    aula = str()
    periodo = str()
    _SEPARADOR_DATOS = '|'
    
    def __init__(self, cadenaMateria):
        pass

    def __init__(self, centro: str, sesion: str, horas: str, dias: str, edificio: str, aula: str, periodo: str):
        self.centro = centro
        self.sesion = sesion
        self.horas = horas
        self.dias = dias
        self.edificio = self._limpiarEdificio(edificio, centro)
        self.aula = aula
        self.periodo = periodo

    def _limpiarEdificio(self, edificio: str, centro: str):
        if "Edificio" in edificio or "Virtual" in edificio:
            if "Virtual" in edificio:
                edificio = edificio.split("Virtual")[-1].strip()
                edificio = "{}ESV{}".format(centro, edificio)
            else:
                edificio = edificio.split("Edificio")[-1].strip()
                edificio = "{}ED{}".format(centro, edificio)
        return edificio

    def obtenerSesion(self):
        sesion = self.sesion
        sesion = sesion if isinstance(sesion, int) else int(sesion[1:])
        return sesion

    def obtenerHoras(self):
        horas = self.horas
        textoHoras = horas
        if textoHoras.find("-") != -1 and textoHoras.find(":") == -1:
            horasIni = f"{int(horas[0:2]):02d}:{int(horas[2:4]):02d}"
            horasFin = f"{int(horas[5:7]):02d}:{int(horas[7:9]):02d}"
            textoHoras = f"{horasIni} - {horasFin}"

        return textoHoras

    def obtenerDias(self):
        dias = []
        diasTexto = self.dias
        if "." in diasTexto:
            diasClave = {
                "L": "Lunes",
                "M": "Martes",
                "I": "Miércoles",
                "J": "Jueves",
                "V": "Viernes",
                "S": "Sábado",
            }
            diasSIIAU = re.sub(r"[\.\s]+", "", diasTexto)
            for letra in diasSIIAU:
                if letra in diasClave:
                    dias.append(diasClave[letra])
            diasTexto = ", ".join(dias)

        return diasTexto

    def obtenerEdificio(self):
        edificio = self.edificio
        centro = self.centro
        if not ("Edificio" in edificio or "Virtual" in edificio):
            coincVirt = re.match(fr"^{centro}ESV|^VIRTU\w*", edificio)
            if coincVirt is not None:
                edificio = re.sub(fr"^{centro}ESV|^VIRTU\w*", "", edificio)
                edificio = f"Virtual {edificio}"
            else:
                edificio = re.sub(fr"^{centro}(ED)?", "", edificio)
                edificio = f"Edificio {edificio}"
        return edificio

    def obtenerAula(self):
        aula = self.aula
        textoAula = aula
        partes_aula = re.match("([A-Za-z]+)(\d+)", aula)
        if partes_aula is not None:
            alfa = partes_aula[1]
            num = int(partes_aula[2])
            textoAula = f"{alfa}{num:02d}"

        return textoAula

    def obtenerPeriodo(self):
        return self.periodo

    def __str__(self):
        salida = str()
        datos = [
            self.centro,
            self.sesion,
            self.horas,
            self.dias,
            self.edificio,
            self.aula,
            self.periodo,
        ]
        
        salida = self._SEPARADOR_DATOS.join(datos)

        return salida