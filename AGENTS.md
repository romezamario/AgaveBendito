# AGENTS.md — Guía operativa base para Codex en este repositorio

## Propósito
Este archivo define cómo debe trabajar cualquier agente (Codex) en este repo para mantener documentación viva, trazabilidad y aprendizaje continuo.

## Reglas obligatorias
1. **Actualizar siempre documentación**:
   - Si hay cambio en código/configuración/contenido/proceso, actualizar también `README.md` y este `AGENTS.md`.
2. **Registrar aprendizaje operativo**:
   - En cada ejecución con cambios, agregar una entrada en la sección “Bitácora de aprendizaje del agente”.
3. **No exponer secretos**:
   - Nunca imprimir ni versionar credenciales, claves, salts o tokens.
4. **Cambio pequeño, evidencia clara**:
   - Explicar qué cambió, por qué, qué riesgo hay y cómo se validó.
5. **Consistencia de idioma**:
   - Documentación principal en español.

## Flujo de trabajo esperado por ejecución
1. Revisar contexto del repositorio y archivos de gobierno (`README.md`, `AGENTS.md`).
2. Identificar alcance del cambio y dimensiones afectadas.
3. Ejecutar cambios mínimos necesarios.
4. Validar (lint/tests/smoke checks según aplique).
5. Actualizar documentación:
   - README: dimensiones impactadas + plantilla de actualización.
   - AGENTS: nueva entrada en bitácora de aprendizaje.
6. Commit con mensaje claro y atómico.

## Checklist obligatorio antes de cerrar una tarea
- [ ] Cambio funcional aplicado (si fue solicitado).
- [ ] `README.md` actualizado.
- [ ] `AGENTS.md` actualizado con aprendizaje nuevo.
- [ ] Validaciones ejecutadas y registradas.
- [ ] Riesgos y pendientes documentados.

## Política de documentación viva
Cuando haya cambios, documentar como mínimo:
- Qué se tocó.
- Qué dimensión(es) del README se afectaron.
- Qué aprender para próximas ejecuciones.
- Qué se debe vigilar post-cambio.

## Plantilla de entrada rápida (usar en cada ejecución con cambios)
```md
### YYYY-MM-DD — <título corto>
- Contexto:
- Cambio aplicado:
- Dimensiones README impactadas:
- Validación realizada:
- Riesgo/limitación:
- Aprendizaje clave para próximos agentes:
- Acción sugerida siguiente:
```

## Bitácora de aprendizaje del agente

### 2026-03-29 — Bootstrap documental inicial
- Contexto: se solicitó crear base de README y AGENTS para actualización continua.
- Cambio aplicado: creación de `README.md` con 20 dimensiones y `AGENTS.md` con flujo/checklist/bitácora.
- Dimensiones README impactadas: [18, 19, 20] y estructura base de gobernanza documental.
- Validación realizada: revisión de estructura del repo y presencia de componentes WordPress.
- Riesgo/limitación: falta definir propietarios por dimensión y flujo de despliegue formal.
- Aprendizaje clave para próximos agentes: cada cambio debe cerrar con doble actualización (`README.md` + `AGENTS.md`) y una lección concreta.
- Acción sugerida siguiente: completar responsables, runbooks de incidentes y política de backups/restores.

### 2026-03-29 — Ajuste de fallback de color en mini-cart
- Contexto: se solicitó ubicar y ajustar el color del botón/área del mini-cart a `#9b5c8f`.
- Cambio aplicado: alineación del fallback visual en `class-frontend.php` y `assets/css/frontend.css` hacia `rgba(155, 92, 143, 0.97)`.
- Dimensiones README impactadas: [2, 3, 11, 15, 20].
- Validación realizada: localización de claves `_wcspc_bg_colors` con `rg`, revisión de archivos afectados y diff de git.
- Riesgo/limitación: el valor activo en producción puede estar sobrescrito por opciones guardadas en base de datos de WordPress.
- Aprendizaje clave para próximos agentes: para cambios de color en este plugin, revisar defaults, CSS base y opciones persistidas en admin.
- Acción sugerida siguiente: confirmar en `/wp-admin/admin.php?page=wcspc` y guardar ajustes para propagar el valor en la opción persistida.
