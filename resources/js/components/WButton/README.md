# w-button (PrimeVue)

Componente de botón personalizado basado en `PrimeVue` (`<Button />`), inspirado en `XButton`. Permite flexibilidad completa con soporte para slots, íconos, colores, tooltip, y estilos dinámicos.

---

## ✅ Props disponibles

| Prop           | Tipo      | Default     | Descripción |
|----------------|-----------|-------------|-------------|
| `label`        | `String`  | `-`         | Texto principal del botón |
| `icon`         | `String`  | `-`         | Icono izquierdo (`pi pi-*`) |
| `iconRight`    | `String`  | `-`         | Icono derecho (`pi pi-*`) |
| `type`         | `String`  | `null`      | Variante semántica (`primary`, `success`, etc.) |
| `color`        | `String`  | `null`      | Color personalizado (no se usa si `type` está definido) |
| `textColor`    | `String`  | `null`      | Color del texto manual |
| `outlined`     | `Boolean` | `false`     | Estilo borde |
| `rounded`      | `Boolean` | `false`     | Estilo redondeado |
| `raised`       | `Boolean` | `false`     | Botón con sombra |
| `text`         | `Boolean` | `false`     | Botón sin borde ni fondo |
| `size`         | `String`  | `'small'`   | Tamaño: `'small'`, `'normal'`, `'large'` |
| `loading`      | `Boolean` | `false`     | Estado de carga |
| `disabled`     | `Boolean` | `false`     | Deshabilita el botón |
| `tooltipText`  | `String`  | `null`      | Texto del tooltip (usa directiva de PrimeVue) |
| `tooltipColor` | `String`  | `null`      | Clase opcional para tooltip personalizado |

---

## 🎯 Slots disponibles

- `default`: Reemplaza todo el contenido del botón.
- `icon`: Slot para ícono izquierdo personalizado.
- `label`: Slot para reemplazar solo el texto.
- `icon-right`: Slot para ícono derecho personalizado.

---

## ⚡ Eventos

| Evento | Descripción           |
|--------|-----------------------|
| `click` | Se emite al hacer clic |

---

## 🧩 Ejemplo de uso

```vue
<w-button
  type="success"
  icon="pi-check"
  label="Guardar"
  :loading="isLoading"
  tooltipText="Guardar cambios"
  @click="guardar"
/>
