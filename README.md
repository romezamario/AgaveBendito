# Agave Bendito — Base de documentación viva

> Este README define la **base documental del repositorio** y se debe actualizar en **cada cambio relevante de código, configuración, contenido, despliegue o seguridad**.

## Objetivo
Este repositorio contiene una instalación de WordPress para `agavebendito.com.mx` con plugins de e-commerce y un tema principal. Este documento estandariza la documentación mínima para operar, mantener y evolucionar el proyecto.

## Regla de actualización continua
- Cada PR/commit que modifique comportamiento, configuración o contenido operativo debe actualizar este README.
- Si el cambio no afecta una dimensión, se debe indicar explícitamente “sin cambios”.
- No exponer secretos (claves, contraseñas, tokens, salts, credenciales) en commits ni en documentación.

---

## Las 20 dimensiones de documentación obligatoria

### 1) Contexto de negocio y alcance
- Qué problema resuelve el sitio.
- Perfil de usuario principal.
- Alcance actual del repositorio (sitio WordPress + extensiones instaladas).

### 2) Inventario de componentes
- Core de WordPress.
- Tema activo.
- Plugins críticos (ej. WooCommerce, Elementor, formularios, seguridad, etc.).
- Contenido y carpetas operativas (`uploads`, `languages`, `maintenance`).

### 3) Arquitectura funcional
- Flujo de navegación principal.
- Embudo de compra/checkout (si aplica).
- Integraciones de marketing/comunicación.

```mermaid
flowchart LR
    Inicio[Visitante llega al sitio] --> Catalogo[Explora catálogo]
    Catalogo --> Producto[Detalle de producto]
    Producto --> Carrito[Carrito]
    Carrito --> Checkout[Checkout WooCommerce]
    Checkout --> Pago[Pasarela de pago]
    Pago --> Confirmacion[Confirmación + email]
```

### 4) Arquitectura técnica
- Estructura de carpetas clave.
- Dependencias de servidor (PHP/MySQL/Apache/Nginx).
- Configuración crítica de arranque (`wp-config.php`).

```mermaid
flowchart TD
    Cliente[Navegador] --> Web[Servidor Web]
    Web --> WP[WordPress Core]
    WP --> Tema[Tema activo]
    WP --> Plugins[Plugins]
    WP --> DB[(MySQL/MariaDB)]
    Plugins --> Ext[Servicios externos]
```

### 5) Configuración y entorno
- Variables/constantes relevantes (sin exponer valores sensibles).
- Convenciones por ambiente (local/staging/prod).
- Flags de depuración y restricciones de edición.

### 6) Seguridad y cumplimiento
- Buenas prácticas activas (ej. edición de archivos deshabilitada).
- Riesgos detectados y mitigaciones.
- Política de manejo de secretos y rotación.

### 7) Datos y persistencia
- Entidades clave (usuarios, productos, órdenes, formularios).
- Política de respaldo y restauración.
- Manejo de logs y retención.

```mermaid
erDiagram
    USUARIOS ||--o{ ORDENES : realiza
    ORDENES ||--|{ ITEMS_ORDEN : contiene
    PRODUCTOS ||--o{ ITEMS_ORDEN : referencia
    USUARIOS ||--o{ FORMULARIOS : envia
```

### 8) Integraciones externas
- Servicios conectados (pagos, email, analítica, CRM, etc.).
- Contratos/API dependientes.
- Impacto ante caída de terceros.

```mermaid
flowchart LR
    WP[WordPress/WooCommerce] --> Pago[Gateway de pago]
    WP --> Email[Servicio email transaccional]
    WP --> Analytics[Analítica]
    WP --> CRM[CRM/Marketing]
```

### 9) Operación diaria
- Tareas recurrentes (actualización de plugins, revisión de órdenes, limpieza de caché).
- Checklist de salud del sitio.
- Responsable sugerido por actividad.

```mermaid
flowchart TD
    A[Inicio de día] --> B[Revisar estado del sitio]
    B --> C[Revisar órdenes y pagos]
    C --> D[Revisar errores/logs]
    D --> E[Actualizar plugins críticos si aplica]
    E --> F[Registrar hallazgos]
```

### 10) Build, release y despliegue
- Estrategia de versionado.
- Flujo de despliegue recomendado.
- Pasos de rollback.

```mermaid
flowchart LR
    Dev[Change local] --> Commit[Commit]
    Commit --> PR[Pull Request]
    PR --> Validacion[Validaciones]
    Validacion --> Deploy[Despliegue]
    Deploy --> Smoke[Smoke tests]
    Smoke --> Ok[Operación normal]
    Smoke -->|Falla| Rollback[Rollback]
```

### 11) QA y pruebas
- Pruebas mínimas por cambio (smoke, checkout, formularios, admin).
- Evidencias requeridas (logs/capturas cuando aplique).
- Criterio de aceptación.

```mermaid
flowchart TD
    Cambio[Cambio aplicado] --> Smoke[Smoke home/producto]
    Smoke --> Checkout[Test checkout]
    Checkout --> Admin[Test admin básico]
    Admin --> Evidencia[Registrar evidencia]
    Evidencia --> Aprobacion[Aprobación]
```

### 12) Observabilidad y monitoreo
- Qué métricas vigilar (errores, latencia, conversión, stock, pedidos).
- Dónde revisar errores (`error_log`, logs de plugins, logs WC).
- Umbrales para alertamiento manual/automático.

```mermaid
flowchart LR
    Logs[Logs PHP/WP/WC] --> Revision[Revisión diaria]
    Metricas[Latencia/errores/conversión] --> Revision
    Revision --> Alerta{¿Umbral excedido?}
    Alerta -->|Sí| Incidente[Activar respuesta a incidente]
    Alerta -->|No| Seguimiento[Seguimiento normal]
```

### 13) Rendimiento
- Objetivos de tiempo de carga.
- Prácticas de optimización de imágenes/assets.
- Revisión de plugins con alto consumo.

### 14) Contenido y SEO
- Flujo editorial.
- Gobernanza de URLs, metadatos y schema.
- Controles de indexación en cambios mayores.

### 15) Accesibilidad y UX
- Criterios mínimos (contraste, navegación teclado, etiquetas).
- Revisión de componentes críticos de compra.
- Hallazgos UX pendientes.

### 16) Incidentes y continuidad
- Procedimiento de respuesta a incidentes.
- Escalamiento y tiempos objetivo.
- Guía de recuperación de operación.

```mermaid
flowchart TD
    Deteccion[Detección del incidente] --> Clasificacion[Clasificar severidad]
    Clasificacion --> Contencion[Contener impacto]
    Contencion --> Recuperacion[Recuperar servicio]
    Recuperacion --> RCA[Análisis causa raíz]
    RCA --> Acciones[Acciones preventivas]
```

### 17) Deuda técnica y roadmap
- Deuda priorizada (seguridad, mantenimiento, limpieza de plugins).
- Mejoras planeadas por trimestre.
- Criterios para remover dependencias.

### 18) Gobierno de repositorio
- Convención de commits y PR.
- Regla de actualización de README + AGENTS por cambio.
- Política de revisión de cambios sensibles.

```mermaid
flowchart LR
    Cambio[Propuesta de cambio] --> Revision[Revisión técnica]
    Revision --> Doc[Actualizar README + AGENTS]
    Doc --> Commit[Commit atómico]
    Commit --> PR[PR con evidencia]
```

### 19) Onboarding de colaboradores
- Qué debe leer primero un nuevo integrante.
- Accesos mínimos requeridos.
- Errores comunes y cómo evitarlos.

```mermaid
flowchart TD
    Nuevo[Nuevo colaborador] --> Leer[Leer README + AGENTS]
    Leer --> Accesos[Solicitar accesos mínimos]
    Accesos --> Setup[Validar entorno]
    Setup --> PrimerCambio[Ejecutar primer cambio guiado]
```

### 20) Bitácora de aprendizaje
- Lecciones aprendidas por cambio.
- Decisiones tomadas y trade-offs.
- Riesgos a vigilar en siguientes iteraciones.

```mermaid
flowchart LR
    Cambio[Cambio ejecutado] --> Leccion[Registrar lección]
    Leccion --> Riesgo[Anotar riesgo]
    Riesgo --> Siguiente[Definir acción siguiente]
```

---

## Estado base inicial (versión 0)

### Resumen observado en este repositorio
- Proyecto basado en WordPress con estructura estándar (`wp-admin`, `wp-content`, `wp-includes`).
- Señales de tienda en línea por presencia de WooCommerce y extensiones relacionadas.
- Tema `shopstore` presente.
- Configuración de seguridad básica activa para deshabilitar editor de archivos en dashboard.

### Pendientes inmediatos sugeridos
1. Definir versión de WordPress/PHP/MySQL objetivo.
2. Documentar flujo oficial de despliegue y backup.
3. Crear tablero de monitoreo mínimo (errores PHP, errores checkout, estado de pedidos).
4. Registrar responsables por dimensión.

---

## Plantilla de actualización por cambio
Copiar este bloque en cada actualización significativa:

```md
### Actualización YYYY-MM-DD
- Cambio realizado:
- Dimensiones impactadas: [1, 2, ...]
- Riesgo introducido:
- Mitigación aplicada:
- Validación ejecutada:
- Lección aprendida:
```

## Criterio de “Done” documental
Un cambio se considera completo solo si:
- [ ] Código/configuración actualizado.
- [ ] README actualizado en dimensiones impactadas.
- [ ] AGENTS.md actualizado con aprendizaje operativo.
- [ ] Validación mínima registrada.
