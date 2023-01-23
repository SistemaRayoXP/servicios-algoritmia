#!/bin/python3
# coding: utf-8

# Esta madre es un todo-en-uno que administra materias.
# Puede consultar materias desde SIIAU y actualizarse a sí mismo,
# incluso distinguiendo materias eliminadas en SIIAU.

# Si se ocupa obtener materias desde SIIAU en cualquier
# otro proyecto de Python, utilizar:
# - Esta clase
# - constantes.py
# - Clase Horario
# - Clase Materia
# Y debería de solventar la mayor parte de administración de materias

# Si lo actualizo y por cualquier razón se tiene un formato de guardado
# anterior, utilizar lo de migrador.py

# Pendiente: Hacer que el formato de exportación sea XML,
# JSON es algo inconveniente

import os
import json

import requests
import bs4

from constantes import *

from Horario import Horario
from Materia import Materia

class CatalogoMaterias():
    # Ruta a medio secundario del catálogo de materias
    rutaCatalogoMaterias = str()
    materias = {}
    idPlan = PLAN_DEF

    def __init__(self, idPlan, prevenirAutoActualizacion=False):
        self.idPlan = idPlan
        self.rutaCatalogoMaterias = self.obtenerRutaActualCatalogoMaterias()
        self.cargar()

        if not self.materias and not prevenirAutoActualizacion:
            materiasSiiau = []
            materiasSiiau.extend(
                self.consultarSiiau(CICLO_ACTUAL, CENTRO_DEF, self.idPlan)
            )
            self.actualizarTodo(materiasSiiau)

    def __clasificar__(self, materias: list):
        materiasClasificadas = {}
        for mat in materias:
            if not isinstance(mat, Materia): continue
            if not materiasClasificadas.get(mat.clave):
                materiasClasificadas[mat.clave] = {}

            materiasClasificadas[mat.clave][mat.nrc] = mat

        return materiasClasificadas

    def __aplanarMaterias__(self):
        materiasAplanadas = {}

        for grupo in self.materias.values():
            for clase in grupo.values():
                materiasAplanadas[clase.nrc] = clase

        return materiasAplanadas

    def __materiasADict__(self):
        """
        Convierte la lista de materias a un dict ordenado por clave y NRC
        """
        dictMaterias = {}

        for clave, materia in self.materias.items():
            dictMaterias[clave] = {}

            for seccion in materia.values():
                dictMateria = seccion.obtenerDict()
                dictMaterias[clave][seccion.nrc] = dictMateria

        return dictMaterias

    def __reemplazarEnCatalogo__(self, materiasNuevas: list):
        """
        Establece los enlaces de grupo existentes en un nuevo catálogo de materias
        """
        nuevoCatalogo = {}
        catalogoActual = self.__aplanarMaterias__()
        nrcsCatalogoActual = self.obtenerNrcsCatalogo()

        for mat_nva in materiasNuevas:
            # Verificamos si la materia nueva ya existía
            # en la oferta para establecer el enlace de invitación
            if mat_nva.nrc in nrcsCatalogoActual:
                # Establecemos la url (o lo que tenía) la otra materia
                mat_nva.url = catalogoActual[mat_nva.nrc].url

                # Añadimos la materia
                nuevoCatalogo[mat_nva.nrc] = mat_nva

            # No existía, simplemente añadimos la materia
            else:
                nuevoCatalogo[mat_nva.nrc] = mat_nva

        return nuevoCatalogo

    def __obtenerMateriasEliminadas__(self, nuevoCatalogo: dict):
        catalogoActual = self.__aplanarMaterias__()
        nrcsNuevoCatalogo = nuevoCatalogo.keys()
        materiasEliminadas = {}

        # Iteramos sobre las materias actuales para ver cuáles ya no existen
        for nrc, materia in catalogoActual.items():
            # Si no existe el NRC en las materias obtenidas de SIIAU, la borraron
            if not nrc in nrcsNuevoCatalogo:
                # Avisamos que la borraron
                materia.eliminada = True
                # La añadimos a las que borraron
                materiasEliminadas[nrc] = materia

        return materiasEliminadas

    def __crearMateriaDesdeDict__(self, dictMateria: dict):
        horarios = []
        indicesRequeridos = ['seccion', 'materia', 'cupo', 'disponible',
                             'creditos', 'nrc', 'clave', 'sesion', 'horas',
                             'dias', 'edificio', 'aula', 'periodo',
                             'profesor', 'url']

        for ir in indicesRequeridos:
            if not ir in dictMateria.keys():
                msjError = f"El índice requerido '{ir}' no existe"
                raise IndexError(msjError)

        # Preferiblemente, el valor por defecto no debería de obtenerse
        centro = dictMateria.get("centro", CENTRO_DEF)

        for i in range(len(dictMateria["sesion"])):
            sesion = dictMateria["sesion"][i]
            horas = dictMateria["horas"][i]
            dias = dictMateria["dias"][i]
            edif = dictMateria["edificio"][i]
            aula = dictMateria["aula"][i]
            periodo = dictMateria["periodo"][i]
            horario = Horario(centro, sesion, horas, dias, edif, aula, periodo)
            horarios.append(horario)

        return Materia(centro, dictMateria["nrc"], dictMateria["clave"],
                              dictMateria["materia"], dictMateria["seccion"],
                              dictMateria["creditos"], dictMateria["cupo"],
                              dictMateria["disponible"], horarios,
                              dictMateria["profesor"],
                              dictMateria.get("eliminada", False),
                              dictMateria["url"])

    def __extraerDatosFila__(self, nodo: bs4.Tag):
        datos = []
        for celda in nodo.find_all("td", recursive=False):
            for element in celda.children:
                if element.name == "table":
                    filas = self.__extraerDatosTabla__(element)
                    datos.append(filas)
                else:
                    if not element.text:
                        continue
                    datos.append(element.text.strip())

        return datos

    def __extraerDatosTabla__(self, tabla: bs4.PageElement, saltarFilasInvalidas: bool = False, cantCeldasEsperadas: int = 0):
        tabla = tabla.find("tbody") if tabla.find("tbody") else tabla
        datosTabla = []
        for node in tabla.find_all("tr", recursive=False):
            if saltarFilasInvalidas:
                if len(node.find_all("td", recursive=False)) < cantCeldasEsperadas:
                    continue
            fila = self.__extraerDatosFila__(node)
            if fila:
                datosTabla.append(fila)

        return datosTabla

    def obtenerNrcsCatalogo(self):
        return list(self.__aplanarMaterias__().keys())

    def obtenerRutaActualCatalogoMaterias(self):
        cadenaCatalogo = f"{self.idPlan}_{CICLO_ACTUAL}.json"

        rutaCatalogo = os.path.join(CARPETA_DATOS, cadenaCatalogo)

        return rutaCatalogo

    def cargar(self, ruta: str = None):
        try:
            ruta = self.rutaCatalogoMaterias if ruta is None else ruta

            if os.path.exists(ruta):
                listaMaterias = []

                with open(self.rutaCatalogoMaterias, "r") as f:
                    dictMaterias = json.loads(f.read())

                for materia in dictMaterias.values():
                    for clase in materia.values():
                        try:
                            materia = self.__crearMateriaDesdeDict__(clase)
                            listaMaterias.append(materia)

                        except IndexError:
                            print(f"Error cargando NRC#{clase['nrc']}")

                self.materias = self.__clasificar__(listaMaterias)

        except Exception:
            import traceback
            print("Error: No se pudieron cargar las materias desde disco.")
            print("Detalles:")
            print(traceback.format_exc())

    def guardar(self, ruta: str = None):
        """
        Guarda el catálogo de materias en disco
        """
        guardadoCorrecto = True

        try:
            dictMaterias = self.__materiasADict__()
            ruta = self.rutaCatalogoMaterias if ruta is None else ruta

            with open(ruta, "w") as f:
                f.write(json.dumps(dictMaterias, indent=2))

        except Exception:
            guardadoCorrecto = False
            import traceback
            print("Error: No se pudieron guardar las materias en disco")
            print("Detalles:")
            print(traceback.format_exc())

        return guardadoCorrecto

    def actualizarPlanSeleccionado(self, nuevoIdPlan: str):
        self.idPlan = nuevoIdPlan
        self.rutaCatalogoMaterias = self.obtenerRutaActualCatalogoMaterias()

    def actualizar(self, materia: Materia):
        self.materias[materia.clave][materia.nrc] = materia
        self.guardar()

    def actualizarTodo(self,
                       materiasNuevas: list,
                       conservarEliminadas: bool = True):
        """Actualiza el catálogo con un dict de las materias existentes
        en SIIAU, Se eliminarán las que no estén presentes en el
        nuevo catálogo a menos de que se indique lo contrario

        Args:
            materiasNuevas (list):
            El nuevo catálogo con objetos de las materias existentes

            conservarEliminadas (bool, optional):
            Si establecido en True, conservará las materias que se haya
            eliminado de SIIAU. Útil si alguna clase será reemplazada
            y el enlace de invitación se desea conservar.
            Por defecto es True.
        """
        # Aquí se almacenarán las materias existentes en la oferta
        nuevoCatalogo = self.__reemplazarEnCatalogo__(materiasNuevas)

        # Usualmente no queremos conservar las materias eliminadas
        # cuando ya empezó el ciclo y no las restablecieron
        # Previo a inicio de cursos, puede que las restablezcan
        # y hay que conservarlas
        if conservarEliminadas:
            nuevoCatalogo.update(
                self.__obtenerMateriasEliminadas__(nuevoCatalogo))

        # Procesamos el nuevo catálogo
        materias = self.__clasificar__(list(nuevoCatalogo.values()))
        self.materias = materias

        # Guardamos el catálogo
        self.guardar()

    def obtener(self, nrc: int) -> Materia:
        catalogoMaterias = self.__aplanarMaterias__()
        nrcsCatalogoMaterias = self.obtenerNrcsCatalogo()

        if not nrc in nrcsCatalogoMaterias:
            raise IndexError("El NRC no se encuentra en el catálogo")

        return catalogoMaterias[nrc]

    def obtenerPorClave(self, clave: str):
        return self.materias.get(clave, {})

    def obtenerTodo(self, clasificar: bool = True):
        """Obtiene todas las materias del catálogo

        Args:
            clasificar (bool, optional):
            Clasificar las secciones por clave.
            Por defecto es True.

        Retorna:
            dict: Un dict de objetos materia accesibles por NRC
        """
        materiasCatalogo = self.materias

        if not clasificar:
            materiasCatalogo = self.__aplanarMaterias__()

        return materiasCatalogo

    def consultarSiiau(self,
                       ciclo: str = CICLO_ACTUAL,
                       centro: str = CENTRO_DEF,
                       materia: str = "",
                       cantResultados: int = CANT_RESULT_DEF,
                       indiceInicio: int = 0):
        """Realiza una consulta de oferta académica al SIIAU
        y retorna una lista con los resultados

        Args:
            ciclo (str, optional):
            Ciclo del que consultar. Defaults to CICLO_ACTUAL.

            centro (str, optional):
            Letra del centro a consultar. Defaults to CENTRO_DEF.

            plan (str, optional):
            Plan de estudios del que consultar. Defaults to PLAN_DEF.

            materia (str, optional):
            Clave de la materia a que consultar. Defaults to "".

            cantResultados (int, optional):
            Cantidad máxima de resultados a devolver. Defaults to CANT_RESULT_DEF.

            indiceInicio (int, optional):
            Un pivote desde el que retornar resultados. Defaults to 0.

        Returns:
            list[Materia]: Una lista con objetos materia,
            convertibles a diccionarios
        """
        materias_obtenidas = []
        datos_post = {
            'ciclop': ciclo,
            'cup': centro,
            'majrp': self.idPlan,
            'crsep': materia,
            'mostrarp': cantResultados,
            'p_start': indiceInicio,
        }
        if indiceInicio < 1:
            datos_post.pop("p_start")
        try:
            response = requests.post(APP_SIIAU, data=datos_post)
        except (ConnectionError, requests.exceptions.ConnectionError):
            print("Ocurió un error conectando con SIIAU.")
            respuesta = ""
        else:
            respuesta = response.text

        if respuesta:
            soup = bs4.BeautifulSoup(respuesta, "html5lib")
            table = soup.body.table
            datos = self.__extraerDatosTabla__(table)

            for entrada in datos:
                horarios = []
                
                try:
                    for fila in entrada[8]:
                        horario = Horario(centro, fila[0], fila[1], fila[2],
                                        fila[3], fila[4], fila[5])
                        horarios.append(horario)

                    profesor = entrada[10][0][1] if len(entrada) > 10 else ""

                    materia_obj = Materia(centro, int(entrada[0]), entrada[1],
                                        entrada[2], entrada[3], int(entrada[4]),
                                        int(entrada[5]), int(entrada[6]),
                                        horarios, profesor)
                    materias_obtenidas.append(materia_obj)
                except Exception:
                    import traceback
                    print("Error añadiendo una materia. Detalles:")
                    print("Contenido de entrada:", str(entrada))
                    print(traceback.format_exc())

        return materias_obtenidas
