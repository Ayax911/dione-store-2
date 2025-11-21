{{-- Componente Impacto Personal --}}
@if(isset($impacto) && $impacto['total_prendas'] > 0)
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 2rem; color: white; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);">
    
    <div class="text-center mb-4">
        <h2 style="font-weight: 800; margin-bottom: 0.5rem;">Tu Impacto Ambiental</h2>
        <p style="opacity: 0.9; font-size: 1.1rem;">Contribución a la sostenibilidad</p>
    </div>

    <div class="row g-4">
        
        <div class="col-md-4">
            <div style="background: rgba(255,255,255,0.15); border-radius: 15px; padding: 2rem; text-align: center; height: 100%;">
                <div style="font-size: 1rem; opacity: 0.9; margin-bottom: 0.5rem;">CO2 Total Ahorrado</div>
                <div style="font-size: 2.5rem; font-weight: 800; line-height: 1;">
                    {{ number_format($impacto['total_co2_ahorrado'], 1) }}
                </div>
                <div style="font-size: 1rem; opacity: 0.9; margin-top: 0.5rem;">kilogramos</div>
            </div>
        </div>

        <div class="col-md-4">
            <div style="background: rgba(255,255,255,0.15); border-radius: 15px; padding: 2rem; text-align: center; height: 100%;">
                <div style="font-size: 1rem; opacity: 0.9; margin-bottom: 0.5rem;">Prendas Publicadas</div>
                <div style="font-size: 2.5rem; font-weight: 800; line-height: 1;">
                    {{ $impacto['total_prendas'] }}
                </div>
                <div style="font-size: 1rem; opacity: 0.9; margin-top: 0.5rem;">prendas</div>
            </div>
        </div>

        <div class="col-md-4">
            <div style="background: rgba(255,255,255,0.15); border-radius: 15px; padding: 2rem; text-align: center; height: 100%;">
                <div style="font-size: 1rem; opacity: 0.9; margin-bottom: 0.5rem;">Promedio por Prenda</div>
                <div style="font-size: 2.5rem; font-weight: 800; line-height: 1;">
                    {{ number_format($impacto['promedio_por_prenda'], 1) }}
                </div>
                <div style="font-size: 1rem; opacity: 0.9; margin-top: 0.5rem;">kg CO2</div>
            </div>
        </div>
    </div>

    <div style="background: rgba(255,255,255,0.1); border-radius: 15px; padding: 1.5rem; margin-top: 2rem; text-align: center;">
        <p style="opacity: 0.95; margin: 0; font-size: 1.05rem;">
            Gracias por contribuir a un futuro más sostenible. Cada prenda que publicas reduce las emisiones de CO2 en la atmósfera.
        </p>
    </div>
</div>
@endif
```

### Paso 5.5: Guardar archivo
```
Ctrl+S
```

### ✅ Verificación Fase 5:
```
- Carpeta components/ existe
- huella-carbono-simple.blade.php creado
- impacto-personal-simple.blade.php creado
- Ambos archivos sin errores
```

---

## 📋 FASE 6: ACTUALIZAR SHOW.BLADE.PHP (2 minutos)

### Paso 6.1: Abrir archivo
```
Ruta: resources/views/show.blade.php
```

### Paso 6.2: Buscar "Productos Similares"
```
CTRL+F → buscar: "Productos Similares"