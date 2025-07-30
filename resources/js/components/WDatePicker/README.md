# WDatePicker

Componente de fecha personalizado basado en [PrimeVue DatePicker](https://www.primefaces.org/primevue/datepicker/), diseñado para mantener consistencia visual y de estructura con el resto de componentes personalizados (`WSelect`, `WInputText`, etc.).

---

## ✨ Características

- Compatible con `v-model`
- Soporte para vistas: `date`, `month`, `year`
- Etiqueta (`label`) con soporte de campo requerido
- Mensaje de error
- Estilos consistentes y clases BEM
- Personalización del formato de salida

---

## ⚙️ Props

| Prop         | Tipo            | Descripción                                   | Por defecto |
|---------------|-----------------|-----------------------------------------------|-------------|
| `modelValue` | `Date` \| `String` | Valor del modelo                            | `null`      |
| `label`      | `String`        | Etiqueta del campo                           | `''`        |
| `name`       | `String`        | Nombre del input                             | `''`        |
| `placeholder`| `String`        | Placeholder del input                        | `''`        |
| `id`         | `String`        | ID del input                                 | Generado automáticamente |
| `disabled`   | `Boolean`       | Deshabilitar el input                        | `false`     |
| `autofocus`  | `Boolean`       | Autofocus al cargar                          | `false`     |
| `error`      | `String`        | Mensaje de error                             | `''`        |
| `minDate`    | `Date`          | Fecha mínima seleccionable                   | `null`      |
| `view`       | `String`        | Vista del calendario: `date`, `month`, `year` | `date`    |
| `required`   | `Boolean`       | Mostrar asterisco en etiqueta               | `false`     |

---

## 💬 Events

| Evento              | Payload       | Descripción                        |
|---------------------|---------------|------------------------------------|
| `update:modelValue` | `String`      | Valor formateado seleccionado     |

---

## 🧑‍💻 Ejemplo de uso

\`\`\`vue
<script setup>
import WDatePicker from '@/components/WDatePicker/WDatePicker.vue'
import { ref } from 'vue'

const selectedDate = ref('')
</script>

<template>
  <WDatePicker
    v-model="selectedDate"
    label="Fecha de nacimiento"
    placeholder="Selecciona una fecha"
    required
    error="Campo obligatorio"
    view="date"
  />
</template>
\`\`\`

---

## 🎨 Estilos

El componente utiliza clases BEM:

- `w-date-picker`: contenedor principal
- `w-date-picker__label`: etiqueta
- `w-date-picker__required`: asterisco rojo
- `w-date-picker__input`: input principal
- `w-date-picker__error`: mensaje de error

**Importa el archivo SCSS correspondiente:**

\`\`\`scss
@import "components/WDatePicker/WDatePicker";
\`\`\`

---

## 📄 Licencia

MIT
