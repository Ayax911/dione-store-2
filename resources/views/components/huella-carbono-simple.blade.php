{{-- Componente Huella de Carbono --}}
@if($huella)
<div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 15px; padding: 2rem; color: white; margin: 2rem 0; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);">
    
    <div class="text-center mb-4">
        <h3 style="font-weight: 700; font-size: 1.8rem; margin-bottom: 0.5rem;">
            Impacto Ambiental
        </h3>
        <p style="opacity: 0.9; font-size: 1.1rem;">Comprando segunda mano reduces emisiones de CO2</p>
    </div>

    <div style="background: rgba(255,255,255,0.2); border-radius: 15px; padding: 2rem; margin-bottom: 2rem; backdrop-filter: blur(10px);">
        <div style="text-align: center;">
            <div style="font-size: 1rem; opacity: 0.9; margin-bottom: 0.5rem;">CO2 Ahorrado</div>
            <div style="font-size: 3.5rem; font-weight: 800; line-height: 1;">
                {{ number_format($huella->co2_ahorrado, 1) }}
            </div>
            <div style="font-size: 1.3rem; font-weight: 600;">kilogramos</div>
        </div>
    </div>

    <div style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 1.5rem; backdrop-filter: blur(10px);">
        <h5 style="font-weight: 600; margin-bottom: 1rem;">Comparación de Emisiones</h5>
        
        <div class="row text-center mb-3">
            <div class="col-6">
                <div style="font-size: 0.9rem; opacity: 0.8; margin-bottom: 0.5rem;">Prenda Nueva</div>
                <div style="font-size: 1.8rem; font-weight: 700;">{{ number_format($huella->co2_total_nueva, 1) }}</div>
                <div style="font-size: 0.9rem;">kg CO2</div>
            </div>
            <div class="col-6">
                <div style="font-size: 0.9rem; opacity: 0.8; margin-bottom: 0.5rem;">Segunda Mano</div>
                <div style="font-size: 1.8rem; font-weight: 700;">{{ number_format($huella->co2_segunda_mano, 1) }}</div>
                <div style="font-size: 0.9rem;">kg CO2</div>
            </div>
        </div>

        <div style="height: 8px; background: rgba(255,255,255,0.2); border-radius: 10px; overflow: hidden; position: relative; --progress-width: {{ $huella->porcentaje_ahorro ?? 0 }}%;">
            <div style="background: #4ade80; height: 100%; width: var(--progress-width); border-radius: 10px; transition: width 0.3s ease;"></div>
        </div>
        <div class="text-center mt-2" style="font-weight: 600;">
            {{ number_format($huella->porcentaje_ahorro, 0) }}% menos emisiones
        </div>
    </div>

    <div class="mt-3">
        <button class="btn w-100" style="background: rgba(255,255,255,0.2); color: white; border: none; border-radius: 10px; padding: 0.75rem;" type="button" data-bs-toggle="collapse" data-bs-target="#desgloseHuella">
            Ver desglose detallado
        </button>
        
        <div class="collapse mt-3" id="desgloseHuella">
            <div style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 1.5rem; backdrop-filter: blur(10px);">
                <h6 style="font-weight: 600; margin-bottom: 1rem;">Información del Cálculo</h6>
                
                <div style="margin-bottom: 0.75rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Fabricación:</span>
                        <span style="font-weight: 600;">{{ number_format($huella->co2_fabricacion, 1) }} kg CO2</span>
                    </div>
                    <small style="opacity: 0.8;">Material: {{ $prenda->material }}</small>
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Categoría:</span>
                        <span style="font-weight: 600;">{{ $huella->categoria_calculo }}</span>
                    </div>
                </div>

                <hr style="border-color: rgba(255,255,255,0.3); margin: 1rem 0;">

                <div style="font-size: 0.85rem; opacity: 0.8;">
                    <p style="margin-bottom: 0.5rem;"><strong>Metodología:</strong> Carbon Trust</p>
                    <p style="margin-bottom: 0;">Los cálculos se basan en estudios científicos sobre el ciclo de vida de prendas textiles.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4" style="opacity: 0.8; font-size: 0.9rem;">
        <p style="margin: 0;">Cada compra de segunda mano ayuda al medio ambiente</p>
    </div>
</div>
@endif