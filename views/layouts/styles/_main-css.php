<?php

// CSS para Fluent Design, modales y sidebar
$this->registerCss("
/* Fluent Design - Efecto Blur para Header y Footer */
.fluent-header {
    background: rgba(255, 255, 255, 0.7) !important;
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
}

.fluent-footer {
    background: rgba(255, 255, 255, 0.7) !important;
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0 -2px 20px rgba(0, 0, 0, 0.08);
}

/* Mejoras adicionales para el efecto Fluent */
.fluent-header::before,
.fluent-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0.6) 100%);
    pointer-events: none;
    z-index: -1;
}

/* Ajustes para mejor legibilidad */
.fluent-header .navbar-brand,
.fluent-footer a {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Efecto hover mejorado para enlaces del footer */
.fluent-footer a:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

/* Soporte para navegadores que no soportan backdrop-filter */
@supports not (backdrop-filter: blur(40px)) {
    .fluent-header {
        background: rgba(255, 255, 255, 1) !important;
    }
    
    .fluent-footer {
        background: rgba(255, 255, 255, 1) !important;
    }
}

/* Dark mode support (opcional) */
@media (prefers-color-scheme: dark) {
    .fluent-header {
        background: rgba(255, 255, 255, 0.7) !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .fluent-footer {
        background: rgba(255, 255, 255, 0.7) !important;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .fluent-header::before,
    .fluent-footer::before {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0.6) 100%);
    }
}

/* Sidebar Styles */
#sidebar {
    transition: all 0.3s ease;
}

/* Scrollbar personalizado para sidebar */
.sidebar-body::-webkit-scrollbar {
    width: 6px;
}

.sidebar-body::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-body::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
}

.sidebar-body::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

/* Sidebar Navigation Items */
.sidebar-nav-item {
    transition: all 0.2s ease;
    position: relative;
    background: transparent;
}

.sidebar-nav-item:hover {
    background: rgba(0, 0, 0, 0.03);
    transform: translateX(4px);
}

.sidebar-nav-item:active {
    transform: translateX(2px);
}

/* Active state for current page */
.sidebar-nav-item.active {
    background: rgba(0, 0, 0, 0.05);
    font-weight: 600;
}

.sidebar-nav-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 70%;
    border-radius: 0 4px 4px 0;
}

/* Color indicators for each menu item */
.sidebar-nav-item[data-color='#6c757d']:hover,
.sidebar-nav-item[data-color='#6c757d'].active {
    border-left: 3px solid #6c757d;
}

.sidebar-nav-item[data-color='#28a745']:hover,
.sidebar-nav-item[data-color='#28a745'].active {
    border-left: 3px solid #28a745;
}

.sidebar-nav-item[data-color='#dc3545']:hover,
.sidebar-nav-item[data-color='#dc3545'].active {
    border-left: 3px solid #dc3545;
}

.sidebar-nav-item[data-color='#007bff']:hover,
.sidebar-nav-item[data-color='#007bff'].active {
    border-left: 3px solid #007bff;
}

.sidebar-nav-item[data-color='#fd7e14']:hover,
.sidebar-nav-item[data-color='#fd7e14'].active {
    border-left: 3px solid #fd7e14;
}

.sidebar-nav-item[data-color='#6f42c1']:hover,
.sidebar-nav-item[data-color='#6f42c1'].active {
    border-left: 3px solid #6f42c1;
}

.sidebar-nav-item[data-color='#17a2b8']:hover,
.sidebar-nav-item[data-color='#17a2b8'].active {
    border-left: 3px solid #17a2b8;
}

/* Sidebar footer button */
.sidebar-footer .btn {
    transition: all 0.2s ease;
}

.sidebar-footer .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stepper-modal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.stepper-modal::before {
    content: '';
    position: absolute;
    top: 25px;
    left: 0;
    right: 0;
    height: 3px;
    background: #e9ecef;
    z-index: 0;
}

.stepper-progress-modal {
    position: absolute;
    top: 25px;
    left: 0;
    height: 3px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    z-index: 1;
    transition: width 0.3s ease;
    width: 0%;
}

.step-modal {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    flex: 1;
    cursor: pointer;
}

.step-circle-modal {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: white;
    border: 3px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.1rem;
    color: #adb5bd;
    transition: all 0.3s ease;
    margin-bottom: 10px;
}

.step-modal.active .step-circle-modal {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-color: #007bff;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    transform: scale(1.1);
}

.step-modal.completed .step-circle-modal {
    background: #28a745;
    border-color: #28a745;
    color: white;
}

.step-modal.completed .step-circle-modal i {
    display: block;
}

.step-circle-modal i {
    display: none;
}

.step-circle-modal .step-number-modal {
    display: block;
}

.step-modal.completed .step-circle-modal .step-number-modal {
    display: none;
}

.step-label-modal {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 500;
    text-align: center;
}

.step-modal.active .step-label-modal {
    color: #007bff;
    font-weight: 600;
}

.step-modal.completed .step-label-modal {
    color: #28a745;
}

.step-content-modal {
    display: none;
    animation: fadeInModal 0.3s ease;
}

.step-content-modal.active {
    display: block;
}

@keyframes fadeInModal {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.section-title-modal {
    font-size: 1.25rem;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-card-modal {
    border: 3px solid #e9ecef;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.status-card-modal:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.status-card-modal.selected {
    border-color: #007bff;
    background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%);
}

.status-card-modal.status-solvente.selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%);
}

.status-card-modal.status-moroso.selected {
    border-color: #dc3545;
    background: linear-gradient(135deg, #f8d7da 0%, #ffebee 100%);
}

.status-icon-modal {
    margin-bottom: 10px;
}

.status-title-modal {
    font-size: 1.1rem;
    margin-bottom: 5px;
}

/* Estilos para Modal de Recordatorio de Cobros */
.cliente-accordion-item {
    margin-bottom: 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.cliente-accordion-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
}

.cliente-accordion-button {
    font-weight: 600;
    font-size: 1.1rem;
    padding: 20px;
    background: transparent !important;
    box-shadow: none !important;
}

.cliente-accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
    color: #856404;
}

.cliente-info-badge {
    font-size: 0.85rem;
    padding: 5px 12px;
    border-radius: 20px;
    margin-left: 10px;
}

.factura-item {
    background: #f8f9fa;
    border-left: 4px solid #dc3545;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.2s;
    cursor: pointer;
}

.factura-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.factura-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.factura-codigo {
    font-weight: 700;
    font-size: 1.1rem;
    color: #2c3e50;
}

.factura-status-badge {
    background: #dc3545;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.factura-details {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.factura-detail-item {
    text-align: center;
    padding: 10px;
    background: white;
    border-radius: 8px;
}

.factura-detail-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 5px;
}

.factura-detail-value {
    font-weight: 700;
    font-size: 1.1rem;
}

.factura-detail-value.total {
    color: #dc3545;
}

.factura-detail-value.pagado {
    color: #28a745;
}

.factura-detail-value.pendiente {
    color: #ffc107;
}

@media (max-width: 768px) {
    .factura-details {
        grid-template-columns: 1fr;
    }
}

/* Modal Selección de Producto */
.producto-card-modal {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    background: white;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.producto-card-modal:hover {
    border-color: #6c757d;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.producto-card-modal.selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff9 0%, #e8f5e9 100%);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.25);
}

.producto-card-modal.selected::before {
    content: '\f26b';
    font-family: 'bootstrap-icons';
    position: absolute;
    top: 10px;
    right: 10px;
    color: #28a745;
    font-size: 1.5rem;
    font-weight: bold;
}

.producto-card-description {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.producto-card-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e9ecef;
}

.producto-info-item {
    display: flex;
    flex-direction: column;
}

.producto-info-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.producto-info-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: #333;
}

.producto-info-value.stock {
    color: #17a2b8;
}

.producto-info-value.precio {
    color: #28a745;
}

.producto-card-badge {
    display: inline-block;
    background: #6c757d;
    color: white;
    padding: 4px 10px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 10px;
    width: fit-content;
}

.producto-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
}

.filtro-lugar-btn {
    padding: 8px 16px;
    border: 2px solid #e9ecef;
    border-radius: 20px;
    background: white;
    color: #6c757d;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filtro-lugar-btn:hover {
    border-color: #6c757d;
    background: #f8f9fa;
}

.filtro-lugar-btn.active {
    border-color: #28a745;
    background: #28a745;
    color: white;
}

.filtro-lugar-btn i {
    margin-right: 5px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .producto-card-badge {
        font-size: 0.6rem;
        padding: 3px 8px;
        border-radius: 8px;
    }
    
    .producto-card-modal {
        padding: 15px;
    }
    
    .producto-card-title {
        font-size: 0.9rem;
    }
}



/* Modal Lugar - Barra de Progreso */
.progress-bar-container-lugar {
    margin-bottom: 30px;
}

.progress-bar-wrapper-lugar {
    position: relative;
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill-lugar {
    height: 100%;
    background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 10px;
    width: 0%;
}

.progress-info-lugar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
    font-size: 0.9rem;
    color: #6c757d;
}

.progress-step-text-lugar {
    font-weight: 600;
    color: #007bff;
}

.progress-percentage-lugar {
    font-weight: 500;
}

.step-content-lugar {
    display: none;
    animation: fadeInModal 0.3s ease;
}

.step-content-lugar.active {
    display: block;
}
");

//MODAL DE COBROS------------------------------------------------|
$this->registerCss("
/* Estilos para Modal de Detalles de Cobros */
.invoice-summary-modal {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.summary-item-modal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
}

.summary-label-modal {
    font-weight: 500;
    color: #6c757d;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.summary-value-modal {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
}

/* Barra de Progreso del Modal */
.progress-bar-container-modal {
    margin-bottom: 30px;
}

.progress-bar-wrapper-modal {
    position: relative;
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill-modal {
    height: 100%;
    background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 10px;
    position: relative;
}

.progress-bar-fill-modal::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

.progress-info-modal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
    font-size: 0.9rem;
    color: #6c757d;
}

.progress-step-text-modal {
    font-weight: 600;
    color: #007bff;
}

.progress-percentage-modal {
    font-weight: 500;
}

/* Tarjetas de pago en el modal */
.modal-payment-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
}

.modal-payment-card:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
}

.modal-payment-card-header {
    padding: 12px 16px;
    background: #f8f9fa;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.2s ease;
}

.modal-payment-card-header:hover {
    background: #e9ecef;
}

.modal-payment-card-header.expanded {
    background: #e7f3ff;
    border-bottom: 1px solid #e9ecef;
}

.modal-payment-card-title {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.modal-payment-date {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

.modal-payment-amount {
    font-weight: 700;
    color: #28a745;
    font-size: 1rem;
}

.modal-payment-method {
    background: rgba(108, 117, 125, 0.1);
    color: #495057;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.modal-payment-chevron {
    color: #6c757d;
    font-size: 0.9rem;
    transition: transform 0.3s ease;
}

.modal-payment-chevron.rotated {
    transform: rotate(180deg);
}

.modal-payment-card-body {
    padding: 16px;
    background: white;
    display: none;
}

.modal-payment-card-body.show {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-payment-detail-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f1f1f1;
}

.modal-payment-detail-row:last-child {
    border-bottom: none;
}

.modal-payment-detail-label {
    font-weight: 500;
    color: #6c757d;
    font-size: 0.85rem;
}

.modal-payment-detail-value {
    font-weight: 600;
    color: #333;
    font-size: 0.85rem;
    text-align: right;
}

.modal-payment-nota {
    background: #fff3cd;
    padding: 10px;
    border-radius: 6px;
    margin-top: 10px;
    border-left: 3px solid #ffc107;
}

.modal-payment-nota-label {
    font-weight: 600;
    color: #856404;
    font-size: 0.8rem;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.modal-payment-nota-text {
    color: #856404;
    font-size: 0.85rem;
    font-style: italic;
}
");