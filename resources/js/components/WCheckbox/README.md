# WCheckbox

Componente personalizado de checkbox usando [PrimeVue Checkbox](https://primevue.org/checkbox/), estructurado de forma homogénea con los demás componentes personalizados del proyecto.

## Props

| Prop      | Tipo    | Default | Descripción                          |
|------------|---------|---------|-------------------------------------|
| modelValue | Boolean | false   | Valor del checkbox (checked o no). |
| label      | String  | ''      | Texto del label asociado.          |
| name       | String  | ''      | Nombre para el input.             |
| id         | String  | Auto    | ID único generado si no se provee.|
| disabled   | Boolean | false   | Desactiva el checkbox.            |
| required   | Boolean | false   | Indica si es obligatorio.        |

## Eventos

| Evento            | Descripción                                      |
|--------------------|-------------------------------------------------|
| update:modelValue | Emite el nuevo valor booleano cuando cambia. |

## Slots

No tiene slots.

## Uso básico

```vue
<script setup>
import { ref } from 'vue'
import WCheckbox from '@/components/WCheckbox.vue'

const acceptTerms = ref(false)
</script>

<template>
  <WCheckbox
    v-model="acceptTerms"
    label="Acepto los términos y condiciones"
    name="terms"
  />
</template>
```

## Estilos

Puedes personalizar los estilos en `WCheckbox.scss`. Las clases principales son:

- `.w-checkbox`
- `.w-checkbox__label`

---

### ✔️ Consideraciones

- Usa `v-model` para enlazar fácilmente el valor.
- La clase `w-checkbox__label` permite estilizar el texto del label.
- Compatible con PrimeVue 4 y Vue 3.
