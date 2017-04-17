function countdown(id) {
    // Mes 0-11; Días 1-n;
    var fecha = new Date('2018', '03', '14', '00', '00', '00');
    var hoy = new Date();
    var dias = 0;
    var horas = 0;
    var minutos = 0;
    var segundos = 0;

    if (fecha >= hoy) {
        var diferencia = (fecha.getTime() - hoy.getTime()) / 1000;
        dias = Math.floor(diferencia / 86400);
        diferencia = diferencia - (86400 * dias);
        horas = Math.floor(diferencia / 3600);
        diferencia = diferencia - (3600 * horas);
        minutos = Math.floor(diferencia / 60);
        diferencia = diferencia - (60 * minutos);
        segundos = Math.floor(diferencia);

        document.getElementById(id).innerHTML = 'Tiempo restante: ' + dias + ' días, ' + horas + ' horas, ' + minutos + ' minutos y ' + segundos + ' segundos.';

        if (dias > 0 || horas > 0 || minutos > 0 || segundos > 0) {
            setTimeout("countdown(\"" + id + "\")", 1000);
        }
    }
    else {
        window.location.replace('https://adsiar.com/expiration');
    }
}