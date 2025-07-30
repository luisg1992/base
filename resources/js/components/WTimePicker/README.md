# WTimePicker

Componente de selector de hora personalizado basado en PrimeVue [`Calendar`](https://primevue.org/calendar/), diseñado para mantener consistencia con el resto de componentes `W*`.

---

## ✨ Props

| Prop        | Tipo            | Descripción                                           | Default                               |
|--------------|-----------------|-------------------------------------------------------|---------------------------------------|
| modelValue  | Date \| String | Valor del componente (puede ser `Date` o `HH:mm`).    | `null`                                |
| label       | String          | Etiqueta superior opcional.                           | `''`                                  |
| name        | String          | Nombre del input.                                     | `''`                                  |
| placeholder | String          | Placeholder que se muestra cuando está vacío.         | `''`                                  |
| id          | String          | ID único para el input.                               | Auto-generado (`w-timepicker-xxxx`). |
| disabled    | Boolean         | Desactiva el componente.                              | `false`                               |
| autofocus   | Boolean         | Auto-focus al renderizar.                             | `false`                               |
| error       | String          | Mensaje de error a mostrar debajo del input.          | `''`                                  |
| required    | Boolean         | Indica si el campo es requerido (solo visual).        | `false`                               |

---

## 🎯 Emits

- `update:modelValue` — Devuelve la hora formateada en `HH:mm` cada vez que el valor cambia.

---

## 💬 Slots

_No utiliza slots._

---

## ✅ Ejemplo de uso

```vue
<template>
  <WTimePicker
    v-model="hora"
    label="Hora de inicio"
    placeholder="Seleccione la hora"
    :required="true"
    error="La hora es obligatoria"
  />
</template>

<script setup>
import { ref } from 'vue'
import WTimePicker from '@/components/form/WTimePicker.vue'

const hora = ref('')
</script>
```

---

## ⚙️ Estilos

Puedes personalizar las clases:

- `w-time-picker__label`
- `w-time-picker__required`
- `w-time-picker__input`
- `w-time-picker__error`

---

## 🗂️ Dependencias

- [PrimeVue](https://primevue.org/) `Calendar`

---

## 💡 Notas

- Usa formato de hora en 24h (`HH:mm`).  
- Devuelve un string formateado (`'14:30'`) para facilitar envíos a backend.  
- Si deseas manejar objetos `Date` directamente, puedes modificar la función `formatTime`.

---

## 🛠️ License

MIT
