# UNIVERSIDAD DE SAN CARLOS DE GUATEMALA
## Facultad de Ingeniería
## Escuela de Ciencias y Sistemas

---

# Proyecto 1
# Intérprete del Lenguaje Golampi

---

### Curso
Organización de Lenguajes y Compiladores 2  

### Estudiante
Keitlyn Valentina Tunchez Castañeda  

### Carnet
202201139  

### Año
2026  

---

# 1. Introducción

El presente documento describe el diseño e implementación del **Intérprete del lenguaje Golampi**, desarrollado como proyecto del curso **Organización de Lenguajes y Compiladores 2**.

Golampi es un lenguaje académico inspirado en la sintaxis de **Golang**, cuyo propósito es aplicar conceptos fundamentales de la teoría de compiladores tales como:

- Análisis léxico
- Análisis sintáctico
- Análisis semántico
- Construcción de tabla de símbolos
- Manejo de errores
- Ejecución de programas

El sistema desarrollado permite a los usuarios escribir código Golampi mediante una **interfaz web**, enviarlo al servidor para su procesamiento y visualizar los resultados generados por el intérprete.

Entre las funcionalidades principales del sistema se encuentran:

- Edición de código fuente
- Ejecución de programas Golampi
- Visualización de salida en consola
- Generación de reportes de errores
- Generación de tabla de símbolos

El proyecto integra múltiples tecnologías como **ANTLR4, PHP, HTML, CSS y JavaScript**, permitiendo construir una herramienta completa para el análisis e interpretación de programas escritos en Golampi.

---

# 2. Objetivos

### 2.1 Objetivo General

Desarrollar un intérprete funcional para el lenguaje Golampi que incluya las fases de análisis léxico, sintáctico y semántico, así como la ejecución de programas y generación de reportes mediante una interfaz gráfica.

### 2.2 Objetivos Específicos

- Implementar el analizador léxico utilizando ANTLR4.
- Implementar el analizador sintáctico mediante una gramática formal del lenguaje.
- Diseñar un sistema de análisis semántico basado en el recorrido del árbol sintáctico.
- Construir una tabla de símbolos que almacene la información de los identificadores.
- Detectar errores léxicos, sintácticos y semánticos.
- Desarrollar una interfaz gráfica que permita la interacción con el intérprete.

---

# 3. Arquitectura del Sistema

El sistema se basa en una arquitectura **cliente-servidor monolítica**, donde la interfaz gráfica se ejecuta en el navegador del usuario y la lógica del intérprete se ejecuta en el servidor.

La comunicación entre ambos componentes se realiza mediante solicitudes HTTP.

## Componentes principales

### Frontend (Interfaz gráfica)

Implementado con:

- HTML
- CSS
- JavaScript

Responsabilidades:

- Proveer un editor de código
- Enviar código al backend
- Mostrar resultados en consola
- Permitir la descarga de reportes

### Backend (Servidor)

Implementado en:

- PHP

Responsabilidades:

- Procesar el código fuente
- Ejecutar el intérprete
- Generar reportes
- Enviar resultados al frontend
 

### Intérprete

Encargado de:

- Análisis léxico
- Análisis sintáctico
- Análisis semántico
- Ejecución del programa


---

# 4. Tecnologías Utilizadas

| Tecnología | Descripción |
|------------|-------------|
| ANTLR4 | Generador de analizadores léxicos y sintácticos |
| PHP | Lenguaje utilizado para implementar el intérprete |
| HTML | Estructura de la interfaz |
| CSS | Diseño visual |
| JavaScript | Interacción con el backend |
| GitHub | Control de versiones |
| Linux | Entorno de desarrollo |

---

# 5. Estructura del Proyecto

El proyecto se encuentra organizado en dos componentes principales: **backend** y **frontend**, siguiendo una separación clara entre la lógica del intérprete y la interfaz gráfica.

La estructura del repositorio es la siguiente:

```
golampi_proyect/
│
├── backend/
│   │
│   ├── vendor/
│   │
│   ├── GolampiLexer.g4
│   ├── GolampiParser.g4
│   │
│   ├── GolampiLexer.php
│   ├── GolampiLexer.tokens
│   │
│   ├── GolampiParser.php
│   ├── GolampiParser.tokens
│   │
│   ├── GolampiParserBaseListener.php
│   ├── GolampiParserListener.php
│   │
│   ├── GolampiParserBaseVisitor.php
│   ├── GolampiParserVisitor.php
│   │
│   ├── InterpreterVisitor.php
│   │
│   ├── lexer.php
│   ├── parser.php
│   ├── semantic.php
│   │
│   ├── run.php
│   ├── execute.php
│   │
│   ├── index.php
│   │
│   ├── composer.json
│   ├── composer.lock
│   │
│   └── test.golampi
│
├── frontend/
│   ├── index.html
│   ├── script.js
│   └── style.css
│
├── Proyecto1.pdf
└── README.md
```

## Descripción de los componentes

### Backend

El directorio **backend** contiene toda la lógica del intérprete Golampi.

#### Gramática del lenguaje

Archivos que definen la gramática del lenguaje Golampi utilizada por ANTLR4:

- `GolampiLexer.g4`
- `GolampiParser.g4`

Estos archivos describen las reglas léxicas y sintácticas del lenguaje.

#### Archivos generados por ANTLR

ANTLR genera automáticamente los analizadores a partir de la gramática:

- `GolampiLexer.php`
- `GolampiParser.php`
- `GolampiParserBaseListener.php`
- `GolampiParserListener.php`
- `GolampiParserBaseVisitor.php`
- `GolampiParserVisitor.php`

Estos archivos permiten construir el árbol sintáctico del programa.

#### Visitor del intérprete

```
InterpreterVisitor.php
```

Este archivo contiene la lógica encargada de **recorrer el árbol sintáctico y ejecutar el programa**, implementando el comportamiento del intérprete.

#### Módulos del intérprete

| Archivo | Descripción |
|------|------|
| lexer.php | Ejecuta el análisis léxico |
| parser.php | Ejecuta el análisis sintáctico |
| semantic.php | Realiza el análisis semántico |
| run.php | Coordina la ejecución del intérprete |
| execute.php | Ejecuta el código Golampi |

#### Otros archivos

| Archivo | Descripción |
|------|------|
| index.php | Punto de entrada del backend |
| composer.json | Configuración de dependencias PHP |
| composer.lock | Registro de dependencias instaladas |
| test.golampi | Archivo de prueba para el intérprete |

---

### Frontend

El directorio **frontend** contiene la interfaz gráfica del sistema.

| Archivo | Descripción |
|------|------|
| index.html | Interfaz principal del editor |
| script.js | Lógica de interacción con el backend |
| style.css | Estilos de la interfaz |

La interfaz permite al usuario:

- escribir código Golampi
- ejecutar programas
- visualizar resultados en consola
- descargar reportes

---

### Archivos adicionales

| Archivo | Descripción |
|------|------|
| Proyecto1.pdf | Enunciado oficial del proyecto |
| README.md | Documentación general del repositorio |
---

# 6. Fases del Intérprete

El procesamiento del código fuente se realiza en tres fases principales.



### 6.1 Análisis Léxico

El análisis léxico transforma el código fuente en una secuencia de **tokens**.

Estos tokens representan los elementos básicos del lenguaje como:

- palabras reservadas
- operadores
- identificadores
- literales

La implementación se realizó utilizando **ANTLR4**, definiendo las reglas en la gramática del lexer.

Ejemplo de tokens:

```
IF
ELSE
FOR
FUNC
RETURN
VAR
CONST
BREAK
CONTINUE
```

Tipos de datos soportados:

```
int32
float32
bool
rune
string
```

---

### 6.2 Análisis Sintáctico

El análisis sintáctico valida que el programa cumpla con la estructura definida por la gramática del lenguaje.

Esta fase se implementó mediante el **parser generado por ANTLR4**.

El parser genera un **árbol de sintaxis abstracta (AST)** que representa la estructura del programa.

Ejemplo de regla de gramática:

```
functionDecl
    : FUNC IDENTIFIER '(' params ')' block
    ;
```

---

### 6.3 Análisis Semántico

El análisis semántico valida aspectos relacionados con el significado del programa.

Entre las validaciones realizadas se encuentran:

- uso de variables declaradas
- compatibilidad de tipos
- control de ámbitos
- duplicación de identificadores
- validación de operaciones

Durante esta fase también se construye la **tabla de símbolos**.

---

# 7. Tabla de Símbolos

La tabla de símbolos almacena información sobre los identificadores utilizados en el programa.

Cada registro contiene:

| Campo | Descripción |
|------|-------------|
| Identificador | Nombre del símbolo |
| Tipo | Tipo de dato |
| Ámbito | Contexto donde fue declarado |
| Valor | Valor asignado |
| Línea | Línea de declaración |
| Columna | Columna de declaración |

Ejemplo:

| Identificador | Tipo | Ámbito | Valor |
|---------------|------|--------|------|
| x | int32 | main | 10 |
| y | int32 | if | 20 |
| suma | función | global | — |

---

# 8. Manejo de Errores

El sistema detecta errores en tres niveles.

---

## 8.1 Errores Léxicos

Ocurren cuando el programa contiene símbolos no válidos.

Ejemplo:

```
@
```

---

## 8.2 Errores Sintácticos

Ocurren cuando la estructura del programa es incorrecta.

Ejemplo:

```
if x > 5
{
```

---

## 8.3 Errores Semánticos

Ocurren cuando el programa viola las reglas del lenguaje.

Ejemplos:

- variable no declarada
- incompatibilidad de tipos
- redeclaración de variables

Ejemplo de reporte de errores:

| # | Tipo | Descripción | Línea | Columna |
|---|------|-------------|------|--------|
| 1 | Léxico | Símbolo no reconocido | 12 | 8 |
| 2 | Sintáctico | Se esperaba ; | 25 | 14 |
| 3 | Semántico | Variable no declarada | 40 | 5 |

---

# 9. Ejecución del Programa

La ejecución del programa comienza en la función:

```
main()
```

Reglas:

- Debe existir exactamente una función `main`.
- No recibe parámetros.
- No retorna valores.

Durante la ejecución se evalúan:

- expresiones
- estructuras de control
- llamadas a funciones
- operaciones aritméticas y lógicas

---

# 10. Interfaz Gráfica

La interfaz gráfica fue diseñada para facilitar la interacción con el intérprete.

Componentes principales:

### Editor de código

Permite escribir programas Golampi.

### Consola

Muestra:

- salida del programa
- mensajes de ejecución

### Barra de acciones

Incluye botones para:

- Nuevo archivo
- Cargar archivo
- Guardar código
- Ejecutar programa
- Limpiar consola

### Panel de reportes

Permite descargar:

- reporte de errores
- tabla de símbolos

![Interfaz](https://i.ibb.co/zW7P81k9/Captura-desde-2026-03-13-09-14-17.png)

---

# 11. Flujo de Procesamiento

El flujo de ejecución del sistema es el siguiente:

1. El usuario escribe código en el editor.
2. El frontend envía el código al servidor.
3. El servidor recibe el código fuente.
4. Se realiza el análisis léxico.
5. Se ejecuta el análisis sintáctico.
6. Se realiza el análisis semántico.
7. Se genera la tabla de símbolos.
8. Se ejecuta la función main.
9. Se produce la salida del programa.
10. El frontend muestra los resultados.

---

# 12. Conclusiones

El desarrollo del intérprete Golampi permitió aplicar conceptos fundamentales de la teoría de compiladores mediante la construcción de un sistema capaz de analizar, interpretar y ejecutar programas escritos en un lenguaje de programación.

El uso de herramientas como ANTLR4 facilitó la generación automática de analizadores léxicos y sintácticos, mientras que PHP permitió implementar la lógica del intérprete y el análisis semántico.

Además, la integración con una interfaz web permitió crear una herramienta interactiva que facilita la ejecución de programas y la visualización de resultados.

Este proyecto fortaleció habilidades en:

- diseño de lenguajes
- construcción de intérpretes
- desarrollo de software
- integración de sistemas
- documentación técnica.
