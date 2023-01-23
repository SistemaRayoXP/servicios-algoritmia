#!/bin/python3
# coding: utf-8

from constantes import *

from Horario import Horario

class Materia:
    centro = str()
    nrc = int()
    clave = str()
    nombre = str()
    seccion = str()
    creditos = int()
    cupo = int()
    disponible = int()
    horarios = []
    profesor = str()
    url = str()
    eliminada = False

    def __init__(self, centro: str, nrc: int, clave: str, nombre: str,
                 seccion: str, creditos: int, cupo: int, disponible: int,
                 horarios: list, profesor: str = "", eliminada: bool = False,
                 url: str = ""):
        self.centro = centro
        self.nrc = nrc
        self.clave = clave
        self.nombre = nombre.upper()
        self.seccion = seccion
        self.creditos = creditos
        self.cupo = cupo
        self.disponible = disponible
        self.horarios = horarios
        self.profesor = profesor.upper()
        self.url = url
        self.eliminada = eliminada

    def indiceValido(self, i, lista):
        return (i is not None and 0 <= i < len(lista))

    def obtenerSesion(self, indiceHorario=None, legible_por_humanos=True):
        sesion = []
        if (self.indiceValido(indiceHorario, self.horarios)):
            horario = self.horarios[indiceHorario]
            sesion = horario.obtenerSesion()
        else:
            for horario in self.horarios:
                if (legible_por_humanos):
                    sesion.append(horario.obtenerSesion())
                else:
                    sesion.append(horario.sesion)

        return sesion

    def obtenerHoras(self, indiceHorario=None, legible_por_humanos=True):
        horas = []
        if (self.indiceValido(indiceHorario, self.horarios)):
            horario = self.horarios[indiceHorario]
            horas = horario.obtenerHoras()
        else:
            for horario in self.horarios:
                if (legible_por_humanos):
                    horas.append(horario.obtenerHoras())
                else:
                    horas.append(horario.horas)

        return horas

    def obtenerDias(self, indiceHorario=None, legible_por_humanos=True):
        dias = []
        if self.indiceValido(indiceHorario, self.horarios):
            horario = self.horarios[indiceHorario]
            dias = horario.obtenerDias()
        else:
            for horario in self.horarios:
                if legible_por_humanos:
                    dias.append(horario.obtenerDias())
                else:
                    dias.append(horario.dias)
        return dias

    def obtenerEdificio(self, indiceHorario=None, legible_por_humanos=True):
        edificios = []
        if (self.indiceValido(indiceHorario, self.horarios)):
            horario = self.horarios[indiceHorario]
            edificios = horario.obtenerEdificio()
        else:
            for horario in self.horarios:
                if legible_por_humanos:
                    edificios.append(horario.obtenerEdificio())

                else:
                    edificios.append(horario.edificio)

        return edificios

    def obtenerAula(self, indiceHorario=None, legible_por_humanos=True):
        aulas = []
        if self.indiceValido(indiceHorario, self.horarios):
            horario = self.horarios[indiceHorario]
            aulas = horario.obtenerAula()
        else:
            for horario in self.horarios:
                if legible_por_humanos:
                    aulas.append(horario.obtenerAula())
                else:
                    aulas.append(horario.aula)
        return aulas

    def obtenerPeriodo(self, indiceHorario=None, legible_por_humanos=True):
        periodos = []
        if self.indiceValido(indiceHorario, self.horarios):
            horario = self.horarios[indiceHorario]
            periodos = horario.obtenerPeriodo()
        else:
            for horario in self.horarios:
                if legible_por_humanos:
                    periodos.append(horario.obtenerPeriodo())
                else:
                    periodos.append(horario.periodo)
        return periodos

    def obtenerNombre(self):
        return self.nombre.title()

    def obtenerProfesor(self):
        return self.profesor.title()

    def obtenerVersionHumana(self):
        """
        Devuelve un diccionario con los detalles de la
        materia en formato legible por humanos
        """
        mat_legible_por_humanos = {
            "centro": self.centro,
            "seccion": self.seccion,
            "materia": self.obtenerNombre(),
            "cupo": self.cupo,
            "disponible": self.disponible,
            "creditos": self.creditos,
            "nrc": self.nrc,
            "clave": self.clave,
            "sesion": self.obtenerSesion(),
            "horas": self.obtenerHoras(),
            "dias": self.obtenerDias(),
            "edificio": self.obtenerEdificio(),
            "aula": self.obtenerAula(),
            "periodo": self.obtenerPeriodo(),
            "profesor": self.obtenerProfesor(),
            "url": self.url,
            "eliminada": self.eliminada,
        }
        return mat_legible_por_humanos

    def obtenerDict(self):
        """
        Devuelve un diccionario con los detalles de la materia
        """
        dictMateria = {
            "centro": self.centro,
            "seccion": self.seccion,
            "materia": self.nombre,
            "cupo": self.cupo,
            "disponible": self.disponible,
            "creditos": self.creditos,
            "nrc": self.nrc,
            "clave": self.clave,
            "sesion": self.obtenerSesion(legible_por_humanos=False),
            "horas": self.obtenerHoras(legible_por_humanos=False),
            "dias": self.obtenerDias(legible_por_humanos=False),
            "edificio": self.obtenerEdificio(legible_por_humanos=False),
            "aula": self.obtenerAula(legible_por_humanos=False),
            "periodo": self.obtenerPeriodo(legible_por_humanos=False),
            "profesor": self.obtenerProfesor(),
            "url": self.url,
            "eliminada": self.eliminada,
        }
        return dictMateria

