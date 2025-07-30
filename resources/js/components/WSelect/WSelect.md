# WSelect

Componente select personalizado basado en [PrimeVue Dropdown](https://www.primefaces.org/primevue/dropdown/), diseñado para mantener consistencia visual con el resto de componentes (por ejemplo `WInputText`, `WDatePicker`, etc.).

---

## ✨ Características

- Compatible con `v-model`
- Etiqueta (`label`) con soporte de campo requerido
- Mensaje de error
- Soporte para placeholder
- Soporta deshabilitado y autofocus
- Estilos consistentes usando clases BEM

---

## ⚙️ Props

| Prop         | Tipo     | Descripción                               | Por defecto |
|---------------|----------|-------------------------------------------|-------------|
| `modelValue` | `any`    | Valor seleccionado                        | `null`      |
| `options`    | `Array`  | Opciones a mostrar en el select           | `[]`        |
| `optionLabel`| `String` | Propiedad del objeto para mostrar         | `'label'`   |
| `optionValue`| `String` | Propiedad del objeto como valor           | `'value'`   |
| `label`      | `String` | Etiqueta superior                         | `''`        |
| `name`       | `String` | Nombre del input                          | `''`        |
| `placeholder`| `String` | Texto placeholder                         | `''`        |
| `id`         | `String` | ID del input                              | Generado automáticamente |
| `disabled`   | `Boolean`| Deshabilitar el select                    | `false`     |
| `autofocus`  | `Boolean`| Autofocus al cargar                       | `false`     |
| `error`      | `String` | Mensaje de error                          | `''`        |
| `required`   | `Boolean`| Mostrar asterisco en etiqueta            | `false`     |

---

## 💬 Events

| Evento              | Payload | Descripción                       |
|---------------------|----------|-----------------------------------|
| `update:modelValue` | `any`    | Valor seleccionado               |

---

## 🧑‍💻 Ejemplo de uso

\`\`\`vue
<script setup>
import WSelect from '@/components/WSelect/WSelect.vue'
import { ref } from 'vue'

const selectedOption = ref(null)
const options = [
  { label: 'Opción 1', value: 1 },
  { label: 'Opción 2', value: 2 },
  { label: 'Opción 3', value: 3 }
]
</script>

<template>
  <WSelect
    v-model="selectedOption"
    :options="options"
    label="Selecciona una opción"
    placeholder="Elige..."
    required
    error="Campo obligatorio"
  />
</template>
\`\`\`

---

## 🎨 Estilos

El componente utiliza clases BEM:

- `w-select`: contenedor principal
- `w-select__label`: etiqueta
- `w-select__required`: asterisco rojo
- `w-select__input`: select principal
- `w-select__error`: mensaje de error

**Importa el archivo SCSS correspondiente:**

\`\`\`scss
@import "components/WSelect/WSelect";
\`\`\`

---

## 📄 Licencia

MIT
