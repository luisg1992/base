
# WInput

Componente personalizado basado en `PrimeVue` v4, diseñado para campos de entrada flexibles y extensibles.  
Permite incluir un botón adicional (con texto o ícono), mostrar errores y manejar etiquetas extra.  

---

## ✨ Características

- Basado en `<InputText>` y `<Button>` de PrimeVue.
- Soporta íconos personalizados.
- Emite eventos claros (`update:modelValue`, `clickButton`).
- Compatible con props comunes de campos de formulario.
- Soporta mensajes de error (single o array).
- Soporte para etiquetas extra (útil para mostrar información adicional).

---

## 🚀 Uso

```vue
<script setup>
import WInput from '@/components/WInput/WInput.vue'
import { ref } from 'vue'

const value = ref('')
const handleClick = (val) => {
  console.log('Botón clickeado, valor actual:', val)
}
</script>

<template>
  <WInput
    v-model="value"
    label="Nombre"
    placeholder="Ingresa tu nombre"
    :required="true"
    :showButton="true"
    buttonText="Buscar"
    @clickButton="handleClick"
    :error="['Este campo es obligatorio.']"
  />
</template>
```

---

## ⚙️ Props

| Prop               | Tipo              | Default              | Descripción                                       |
|--------------------|-------------------|----------------------|---------------------------------------------------|
| `modelValue`       | String, Number    | —                    | Valor del input (v-model).                       |
| `label`            | String            | `''`                 | Texto de etiqueta principal.                      |
| `labelExtra`       | String            | `''`                 | Texto adicional junto al label.                   |
| `labelExtraClass`  | String          | `''`                 | Clase CSS para el label extra.                   |
| `placeholder`      | String            | —                    | Placeholder del input.                            |
| `name`             | String            | —                    | Nombre del campo.                                |
| `type`             | String            | `'text'`            | Tipo de input.                                   |
| `autofocus`        | Boolean          | `false`             | Autoenfoque inicial.                             |
| `autocomplete`     | Boolean          | `false`             | Autocompletar habilitado.                       |
| `id`               | String            | Auto generado      | ID del input.                                    |
| `disabled`         | Boolean          | `false`             | Desactiva el input.                              |
| `required`         | Boolean          | `false`             | Marca como requerido.                            |
| `maxlength`        | String, Number    | —                    | Máximo de caracteres.                           |
| `min`              | String, Number    | —                    | Valor mínimo (numérico).                        |
| `max`              | String, Number    | —                    | Valor máximo (numérico).                        |
| `showButton`       | Boolean          | `false`             | Muestra el botón con texto.                     |
| `showButtonIcon` | Boolean          | `false`             | Muestra el botón solo con ícono.               |
| `buttonText`       | String            | `'+'`              | Texto del botón.                                |
| `buttonIcon`       | String            | `'search'`        | Icono PrimeVue (ej: `search`).                |
| `error`            | String, Array     | `null`             | Mensaje(s) de error a mostrar debajo.          |

---

## 🟢 Eventos

| Evento              | Parámetros     | Descripción                           |
|---------------------|----------------|---------------------------------------|
| `update:modelValue` | value          | Emite nuevo valor del input.         |
| `clickButton`       | value          | Emite al hacer click en el botón, pasando el valor actual. |

---

## 🎨 Estilos

Incluye archivo SCSS: `WInput.scss`.  
Puedes personalizar:

- `.w-input-text`: wrapper principal.
- `.p-inputtext`: estilos del campo de entrada.
- `.p-error`: estilos de error.
- `.x-label`, `.x-label-extra`: etiquetas.

---

## 💡 Ejemplo con botón ícono

```vue
<WInput
  v-model="value"
  label="Buscar"
  placeholder="Buscar..."
  :showButtonIcon="true"
  buttonIcon="search"
  @clickButton="handleClick"
/>
```

---

## ✅ Requisitos

- Vue 3
- PrimeVue 4
- PrimeIcons

---

## 🧩 Créditos

Desarrollado como parte de la librería de componentes personalizados basados en PrimeVue, siguiendo la arquitectura y estilo de `w-button` y otros componentes `W*`.
