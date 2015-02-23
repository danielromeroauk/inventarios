# inventarios

[![Join the chat at https://gitter.im/danielromeroauk/inventarios](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/danielromeroauk/inventarios?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Aplicación realizada con laravel 4.0.10, un framework con sintaxis elegante que facilita el desarrollo de aplicaciones web.

## Captura de pantalla del informe de artículos
![Inventarios](http://fc03.deviantart.net/fs71/f/2014/223/8/d/inventarios_by_danielromeroauk-d7usgrz.png "Inventarios")

La aplicación registra los movimientos de artículos de las sucursales de una empresa, tales como compras, ventas, rotaciones entre sucursales, entregas inmediatas y daños.

# Flujo normal de una rotación

## 1. El remisionero crea la rotación

En este punto es posible cancela la remisión en caso de error.

Estado de rotación: pendiente en origen.

Se pueden agregar comentarios a la rotación.

Los artículos dejan de estar disponible en origen.

## 2. El bodeguero en origen acepta la salida

La remisión no puede cancelarse.

Cualquier usuario aún puede comentar la rotación.

Estado de la rotación: pendiente en destino.

Los artículos no están disponibles en ningún lado.

## 3. El bodeguero en destino acepta la entrada

La remisión no se puede cancelar.

Estado de la rotación: finalizado.

Los artículos aparecen disponibles en destino.

No se puede comentar más.
