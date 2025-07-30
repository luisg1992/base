export function getTriajeParametros() {
    return {
        TriajePresionSis: {
            min: 50, max: 250,
            niveles: [
                { label: 'Hipotensión', max: 90, color: 'text-primary' },    // azul
                { label: 'Normal', max: 140, color: 'text-success' },        // verde
                { label: 'Hipert. leve', max: 180, color: 'text-warning' },  // amarillo
                { label: 'Hipert. severa', max: 250, color: 'text-danger' }  // rojo
            ]
        },
        TriajePresionDia: {
            min: 30, max: 150,
            niveles: [
                { label: 'Hipotensión', max: 60, color: 'text-primary' },
                { label: 'Normal', max: 90, color: 'text-success' },
                { label: 'Hipert.leve', max: 110, color: 'text-warning' },
                { label: 'Hipert. severa', max: 150, color: 'text-danger' }
            ]
        },
        TriajeFrecCardiaca: {
            min: 30, max: 220,
            niveles: [
                { label: 'Bradicardia', max: 60, color: 'text-primary' },
                { label: 'Normal', max: 100, color: 'text-success' },
                { label: 'Taquicardia', max: 220, color: 'text-danger' }
            ]
        },
        TriajeFrecRespiratoria: {
            min: 10, max: 50,
            niveles: [
                { label: 'Bradipnea', max: 12, color: 'text-primary' },
                { label: 'Normal', max: 20, color: 'text-success' },
                { label: 'Taquipnea', max: 50, color: 'text-danger' }
            ]
        },
        TriajeSaturacionOxigeno: {
            min: 50, max: 100,
            niveles: [
                { label: 'Hipoxia severa', max: 85, color: 'text-danger' },
                { label: 'Hipoxia leve', max: 90, color: 'text-warning' },
                { label: 'Normal', max: 100, color: 'text-success' }
            ]
        },
        TriajeTemperatura: {
            min: 30, max: 45,
            niveles: [
                { label: 'Hipotermia', max: 35.9, color: 'text-primary' },
                { label: 'Normal', max: 37.5, color: 'text-success' },
                { label: 'Fiebre', max: 39, color: 'text-warning' },
                { label: 'Fiebre alta', max: 45, color: 'text-danger' }
            ]
        },
        TriajePeso: {
            min: 2, max: 300, // kg
            unidad: 'kg'
        },
        TriajeTalla: {
            min: 30, max: 250, // centímetros
            unidad: 'cm'
        },
        TriajeIMC: {
            min: 10, max: 60,
            niveles: [
                { label: 'Bajo peso', max: 18.5, color: 'text-primary' },
                { label: 'Normal', max: 24.9, color: 'text-success' },
                { label: 'Sobrepeso', max: 29.9, color: 'text-warning' },
                { label: 'Obesidad I', max: 34.9, color: 'text-danger' },
                { label: 'Obesidad II', max: 39.9, color: 'text-danger' },
                { label: 'Obesidad III', max: 60, color: 'text-danger' }
            ]
        }
    }
}
