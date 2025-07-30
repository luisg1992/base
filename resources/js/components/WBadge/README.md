# WBadge (PrimeVue)

Componente personalizado que envuelve y extiende el componente `<Badge />` de PrimeVue, proporcionando una interfaz más limpia y uniforme para mostrar estados, etiquetas y contadores.

---

## ✅ Props disponibles

| Prop       | Tipo              | Default   | Descripción |
|------------|-------------------|-----------|-------------|
| `value`    | `String|Number`   | `-`       | Texto o número a mostrar dentro del badge |
| `severity` | `String`          | `'info'`  | Nivel de color: `primary`, `success`, `info`, `warning`, `danger`, `help` |
| `size`     | `String|null`     | `null`    | Tamaño del badge: `large`, `xlarge`, o `null` |
| `rounded`  | `Boolean`         | `false`   | Muestra el badge como círculo (ideal para contadores) |

---

## 🎯 Slots disponibles

- `default`: Puedes usarlo para insertar contenido completamente personalizado dentro del badge.

---

## 🧪 Ejemplos

### Estado textual

```vue
<WBadge value="Activo" severity="success" />
<WBadge value="Inactivo" severity="danger" />
```

### Badge redondo con número

```vue
<WBadge :value="5" severity="primary" rounded />
```

### Con tamaño grande

```vue
<WBadge :value="count" severity="warning" size="xlarge" />
```

### Contenido personalizado

```vue
<WBadge>
  <span><i class="pi pi-check" /> Aprobado</span>
</WBadge>
```

---

## 🎨 Estilos

Este componente utiliza `WBadge.scss` para sobrescribir y personalizar estilos del badge de PrimeVue. Los estilos incluyen:

- Colores de severidad (`success`, `danger`, etc.)
- Formato en mayúsculas
- Tamaños (`small`, `large`, `xlarge`)
- Soporte para badge redondo (`rounded`)

Importa `WBadge.scss` en tu archivo `app.scss` para que los estilos se apliquen:

```scss
@import 'components/WBadge';
```

---

## 🚀 Requisitos

- [PrimeVue](https://primevue.org/) instalado y configurado.
- Estilos cargados correctamente desde PrimeVue.
- PrimeIcons opcional para íconos dentro de badge.

---