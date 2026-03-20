/* global ukmData */
( function () {
  'use strict';

  document.addEventListener( 'DOMContentLoaded', init );

  /* -------------------------------------------------------
     State
  ------------------------------------------------------- */
  const tooltip   = createEl( 'div', 'ukm-tooltip' );
  let   activeRegion = null;

  /* -------------------------------------------------------
     Bootstrap
  ------------------------------------------------------- */
  function init() {
    document.body.appendChild( tooltip );

    document.querySelectorAll( '.ukm-wrap' ).forEach( wrap => {
      const mode = wrap.dataset.ukmMode || 'modal'; // modal | popover | tooltip
      initMap( wrap, mode );
    } );
  }

  /* -------------------------------------------------------
     Per-map setup
  ------------------------------------------------------- */
  function initMap( wrap, mode ) {
    const svg     = wrap.querySelector( '#ukm-svg-root' );
    const regions = wrap.querySelectorAll( '.ukm-region' );

    regions.forEach( path => {
      const id   = path.dataset.region;
      const info = getRegionData( id );

      /* accessibility */
      path.setAttribute( 'tabindex', '0' );
      path.setAttribute( 'role', 'button' );
      path.setAttribute( 'aria-label', info.name || id );

      /* custom colour */
      if ( info.color ) path.style.fill = info.color;

      /* ---- tooltip on hover (always) ---- */
      path.addEventListener( 'mouseenter', e => showTooltip( e, info.name ) );
      path.addEventListener( 'mousemove',  e => moveTooltip( e ) );
      path.addEventListener( 'mouseleave', hideTooltip );

      /* ---- click / keyboard ---- */
      const activate = () => {
        if ( mode === 'modal' )   openModal( id, info );
        if ( mode === 'popover' ) togglePopover( wrap, path, id, info );
      };

      path.addEventListener( 'click', activate );
      path.addEventListener( 'keydown', e => {
        if ( e.key === 'Enter' || e.key === ' ' ) {
          e.preventDefault();
          activate();
        }
      } );
    } );
  }

  /* -------------------------------------------------------
     Tooltip
  ------------------------------------------------------- */
  function showTooltip( e, name ) {
    if ( !name ) return;
    tooltip.textContent = name;
    tooltip.classList.add( 'ukm-tooltip--visible' );
    moveTooltip( e );
  }

  function moveTooltip( e ) {
    const gap = 14;
    tooltip.style.left = ( e.clientX - tooltip.offsetWidth / 2 ) + 'px';
    tooltip.style.top  = ( e.clientY - tooltip.offsetHeight - gap ) + 'px';
  }

  function hideTooltip() {
    tooltip.classList.remove( 'ukm-tooltip--visible' );
  }

  /* -------------------------------------------------------
     Modal
  ------------------------------------------------------- */
  function openModal( id, info ) {
    closeModal(); // ensure clean state

    const overlay = createEl( 'div', 'ukm-modal-overlay' );
    const modal   = createEl( 'div', 'ukm-modal' );
    overlay.setAttribute( 'role', 'dialog' );
    overlay.setAttribute( 'aria-modal', 'true' );
    overlay.setAttribute( 'aria-label', info.name );

    modal.innerHTML = buildModalHTML( id, info );
    overlay.appendChild( modal );
    document.body.appendChild( overlay );

    /* animate in */
    requestAnimationFrame( () => overlay.classList.add( 'ukm-modal--open' ) );

    /* highlight active region */
    setActiveRegion( id );

    /* close handlers */
    overlay.addEventListener( 'click', e => {
      if ( e.target === overlay ) closeModal();
    } );
    modal.querySelector( '.ukm-modal__close' ).addEventListener( 'click', closeModal );

    /* focus trap */
    modal.querySelector( '.ukm-modal__close' ).focus();

    document.addEventListener( 'keydown', onEsc );
    overlay._ukmCleanup = () => document.removeEventListener( 'keydown', onEsc );

    function onEsc( e ) {
      if ( e.key === 'Escape' ) closeModal();
    }

    overlay._ukmId = id;
    document.body._ukmModal = overlay;
  }

  function closeModal() {
    const overlay = document.body._ukmModal;
    if ( !overlay ) return;

    if ( overlay._ukmCleanup ) overlay._ukmCleanup();

    overlay.classList.remove( 'ukm-modal--open' );
    setActiveRegion( null );

    overlay.addEventListener( 'transitionend', () => overlay.remove(), { once: true } );
    document.body._ukmModal = null;
  }

  function buildModalHTML( id, info ) {
    const stats    = buildStatsHTML( info.stats );
    const linkHref = info.link  || '';
    const linkText = info.link_label || 'Learn more';
    const desc     = info.description || '';

    return `
      <button class="ukm-modal__close" aria-label="Close">&times;</button>
      <p class="ukm-modal__subtitle">United Kingdom</p>
      <h2 class="ukm-modal__title">${ escHtml( info.name || id ) }</h2>
      ${ desc ? `<p class="ukm-modal__body">${ escHtml( desc ) }</p>` : '' }
      ${ stats }
      ${ linkHref ? `<a class="ukm-modal__link" href="${ escAttr( linkHref ) }" target="_blank" rel="noopener noreferrer">${ escHtml( linkText ) }</a>` : '' }
    `;
  }

  function buildStatsHTML( stats ) {
    if ( !stats || !Object.keys( stats ).length ) return '';
    const items = Object.entries( stats ).map( ( [ label, value ] ) => `
      <div class="ukm-stat">
        <div class="ukm-stat__label">${ escHtml( label ) }</div>
        <div class="ukm-stat__value">${ escHtml( String( value ) ) }</div>
      </div>` ).join( '' );
    return `<div class="ukm-modal__stats">${ items }</div>`;
  }

  /* -------------------------------------------------------
     Popover
  ------------------------------------------------------- */
  function togglePopover( wrap, path, id, info ) {
    /* close any existing popover */
    const existing = wrap.querySelector( '.ukm-popover' );
    if ( existing ) {
      if ( existing.dataset.region === id ) {
        dismissPopover( existing );
        setActiveRegion( null );
        return;
      }
      dismissPopover( existing );
    }

    setActiveRegion( id );

    const pop = createEl( 'div', 'ukm-popover' );
    pop.dataset.region = id;
    pop.innerHTML = `
      <div class="ukm-popover__title">${ escHtml( info.name || id ) }</div>
      <div class="ukm-popover__text">${ escHtml( info.description || '' ) }</div>
    `;

    /* position relative to path bounding box within the wrap */
    wrap.style.position = 'relative';
    wrap.appendChild( pop );
    positionPopover( pop, path, wrap );

    requestAnimationFrame( () => pop.classList.add( 'ukm-popover--visible' ) );

    /* dismiss on outside click */
    setTimeout( () => {
      document.addEventListener( 'click', function handler( e ) {
        if ( !pop.contains( e.target ) && e.target !== path ) {
          dismissPopover( pop );
          setActiveRegion( null );
          document.removeEventListener( 'click', handler );
        }
      } );
    }, 0 );
  }

  function positionPopover( pop, path, wrap ) {
    const pRect = path.getBoundingClientRect();
    const wRect = wrap.getBoundingClientRect();
    const popW  = 220;
    let left    = pRect.left - wRect.left + pRect.width / 2 - popW / 2;
    let top     = pRect.top  - wRect.top  - pop.offsetHeight - 8;

    /* keep within wrap bounds */
    left = Math.max( 0, Math.min( left, wRect.width - popW ) );
    if ( top < 0 ) top = pRect.bottom - wRect.top + 8;

    pop.style.left = left + 'px';
    pop.style.top  = top  + 'px';
  }

  function dismissPopover( pop ) {
    pop.classList.remove( 'ukm-popover--visible' );
    pop.addEventListener( 'transitionend', () => pop.remove(), { once: true } );
  }

  /* -------------------------------------------------------
     Helpers
  ------------------------------------------------------- */
  function setActiveRegion( id ) {
    if ( activeRegion ) {
      document.querySelectorAll( `[data-region="${ activeRegion }"]` )
        .forEach( el => el.classList.remove( 'ukm-active' ) );
    }
    activeRegion = id;
    if ( id ) {
      document.querySelectorAll( `[data-region="${ id }"]` )
        .forEach( el => el.classList.add( 'ukm-active' ) );
    }
  }

  function getRegionData( id ) {
    if ( typeof ukmData !== 'undefined' && ukmData.regions && ukmData.regions[ id ] ) {
      return ukmData.regions[ id ];
    }
    return { name: toTitle( id ) };
  }

  function toTitle( slug ) {
    return slug.replace( /-/g, ' ' ).replace( /\b\w/g, c => c.toUpperCase() );
  }

  function createEl( tag, cls ) {
    const el = document.createElement( tag );
    if ( cls ) el.className = cls;
    return el;
  }

  function escHtml( str ) {
    return String( str )
      .replace( /&/g,  '&amp;' )
      .replace( /</g,  '&lt;' )
      .replace( />/g,  '&gt;' )
      .replace( /"/g,  '&quot;' )
      .replace( /'/g,  '&#039;' );
  }

  function escAttr( str ) {
    return encodeURI( String( str ) );
  }

}() );
