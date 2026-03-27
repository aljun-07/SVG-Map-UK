/* global ukmData */
( function () {
  'use strict';

  document.addEventListener( 'DOMContentLoaded', init );

  const SVG_NS = 'http://www.w3.org/2000/svg';

  /* -------------------------------------------------------
     Popup (positioned beside the clicked marker)
  ------------------------------------------------------- */
  const popup = document.createElement( 'div' );
  popup.className = 'ukm-popup';
  popup.setAttribute( 'role', 'dialog' );
  popup.setAttribute( 'aria-modal', 'true' );
  popup.innerHTML =
      '<div class="ukm-popup__header">'
    +   '<span class="ukm-popup__title"></span>'
    +   '<button class="ukm-popup__close" aria-label="Close">&times;</button>'
    + '</div>'
    + '<ul class="ukm-popup__list"></ul>';
  document.body.appendChild( popup );

  const popupTitle = popup.querySelector( '.ukm-popup__title' );
  const popupList  = popup.querySelector( '.ukm-popup__list' );
  const popupClose = popup.querySelector( '.ukm-popup__close' );

  let onPopupClose = null;

  popupClose.addEventListener( 'click', closePopup );
  document.addEventListener( 'keydown', function ( e ) {
    if ( e.key === 'Escape' ) closePopup();
  } );

  function openPopup( name, projects, anchorEl, closeCallback ) {
    const count = projects ? projects.length : 0;

    popupTitle.textContent = name + ( count ? ' \u2013 ' + count + ' project' + ( count !== 1 ? 's' : '' ) : '' );

    popupList.innerHTML = '';
    popupList.style.display = count ? '' : 'none';

    if ( count ) {
      projects.forEach( function ( p ) {
        if ( !p.title ) return;
        const li = document.createElement( 'li' );
        if ( p.url ) {
          const a = document.createElement( 'a' );
          a.href        = p.url;
          a.target      = '_self';
          // a.rel         = 'noopener noreferrer';
          a.textContent = p.title;
          li.appendChild( a );
        } else {
          li.textContent = p.title;
        }
        popupList.appendChild( li );
      } );
    }

    // Position beside the anchor element
    popup.classList.remove( 'ukm-popup--visible' );
    popup.style.top  = '-9999px';
    popup.style.left = '-9999px';

    // Make temporarily visible (off-screen) to measure height
    popup.style.display = 'block';
    requestAnimationFrame( function () {
      positionPopup( anchorEl );
      popup.classList.add( 'ukm-popup--visible' );
      popupClose.focus();
    } );

    onPopupClose = closeCallback || null;
  }

  function positionPopup( anchorEl ) {
    if ( !anchorEl ) return;
    const rect    = anchorEl.getBoundingClientRect();
    const pw      = popup.offsetWidth  || 280;
    const ph      = popup.offsetHeight || 160;
    const margin  = 12;
    const vw      = window.innerWidth;
    const vh      = window.innerHeight;

    // Prefer right of marker; fall back to left
    let left = rect.right + margin;
    if ( left + pw > vw - 8 ) {
      left = rect.left - margin - pw;
    }
    // Clamp to viewport
    left = Math.max( 8, Math.min( left, vw - pw - 8 ) );

    // Vertically centre on the anchor; clamp to viewport
    let top = rect.top + rect.height / 2 - ph / 2;
    top = Math.max( 8, Math.min( top, vh - ph - 8 ) );

    popup.style.left = left + 'px';
    popup.style.top  = top  + 'px';
  }

  function closePopup() {
    popup.classList.remove( 'ukm-popup--visible' );
    if ( onPopupClose ) { onPopupClose(); onPopupClose = null; }
  }

  /* Close popup when page is scrolled */
  window.addEventListener( 'scroll', closePopup, { passive: true } );

  /* -------------------------------------------------------
     Boot
  ------------------------------------------------------- */
  function init() {
    document.querySelectorAll( '.ukm-wrap' ).forEach( initMap );
  }

  /* -------------------------------------------------------
     Per-map initialisation
  ------------------------------------------------------- */
  function initMap( wrap ) {
    const svg = wrap.querySelector( 'svg' );
    if ( !svg ) return;

    const uData         = ( typeof ukmData !== 'undefined' ) ? ukmData : {};
    const data          = uData.regions       || {};
    const markerIcon    = uData.markerIcon    || '';
    const markerSize    = uData.markerSize    || 32;
    const markerColor   = uData.markerColor   || '#e74c3c';
    const mapColor      = uData.mapColor      || '#6f9c76';
    const selectedColor = uData.selectedColor || '#2271b1';
    const inactiveColor = uData.inactiveColor || '#a8c5ad';

    let activeId = null;

    /* ---- Apply global map fill to all paths ---- */
    svg.querySelectorAll( '#features path' ).forEach( function ( path ) {
      const info = data[ path.id ];
      path.style.fill = ( info && info.color ) ? info.color : mapColor;
    } );

    /* ---- Bind region paths ---- */
    svg.querySelectorAll( '#features path' ).forEach( function ( path ) {
      const id   = path.id;
      if ( !id ) return;
      const info = data[ id ] || { name: path.getAttribute( 'name' ) || id, projects: [] };

      path.setAttribute( 'tabindex', '0' );
      path.setAttribute( 'role', 'button' );
      path.setAttribute( 'aria-label', info.name || id );

      path.addEventListener( 'click', function () {
        // Find the marker for this region to use as anchor
        const markerEl = svg.querySelector( '.ukm-marker-group[data-rid="' + id + '"]' );
        handleActivate( id, markerEl || path );
      } );
      path.addEventListener( 'keydown', function ( e ) {
        if ( e.key === 'Enter' || e.key === ' ' ) {
          e.preventDefault();
          const markerEl = svg.querySelector( '.ukm-marker-group[data-rid="' + id + '"]' );
          handleActivate( id, markerEl || path );
        }
      } );
    } );

    /* ---- Place SVG markers ---- */
    placeMarkers( svg, data, markerIcon, markerSize, markerColor, handleActivate );

    /* ---- Deselect when clicking outside the widget and outside popup ---- */
    document.addEventListener( 'click', function ( e ) {
      if ( activeId
        && !wrap.contains( e.target )
        && !popup.contains( e.target ) ) {
        clearActive();
      }
    } );

    /* =====================================================
       Activate / deactivate helpers
    ===================================================== */
    function handleActivate( id, anchorEl ) {
      if ( activeId === id ) {
        clearActive();
        return;
      }
      if ( activeId ) clearActive( /* keepPopup= */ true );
      activeId = id;

      const info     = data[ id ] || { name: id, projects: [] };
      const projects = info.projects || [];

      // Markers: active gets active class; inactive markers get inactiveColor fill
      svg.querySelectorAll( '.ukm-marker-group' ).forEach( function ( m ) {
        const pin = m.querySelector( '.ukm-marker__pin' );
        if ( m.dataset.rid === id ) {
          m.classList.add( 'ukm-marker--active' );
          m.classList.remove( 'ukm-marker--inactive' );
          if ( pin ) pin.style.fill = markerColor;
        } else {
          m.classList.remove( 'ukm-marker--active' );
          m.classList.add( 'ukm-marker--inactive' );
          if ( pin ) pin.style.fill = inactiveColor;
        }
      } );

      // Open positioned popup beside the marker
      openPopup( info.name || id, projects, anchorEl, function () {
        clearActive();
      } );
    }

    function clearActive( keepPopup ) {
      if ( activeId ) {
        svg.querySelectorAll( '.ukm-marker-group' ).forEach( function ( m ) {
          m.classList.remove( 'ukm-marker--active', 'ukm-marker--inactive' );
          const pin = m.querySelector( '.ukm-marker__pin' );
          if ( pin ) pin.style.fill = markerColor;
        } );
      }
      activeId = null;
      if ( !keepPopup ) closePopup();
    }
  }

  /* -------------------------------------------------------
     SVG marker placement
  ------------------------------------------------------- */
  function placeMarkers( svg, data, markerIcon, markerSize, markerColor, onActivate ) {
    let grp = svg.getElementById( 'ukm-markers' );
    if ( !grp ) {
      grp = document.createElementNS( SVG_NS, 'g' );
      grp.id = 'ukm-markers';
      svg.appendChild( grp );
    }

    const scale = markerSize / 32;

    Object.keys( data ).forEach( function ( id ) {
      const info     = data[ id ];
      const projects = info.projects || [];
      if ( !projects.length ) return;

      const path = svg.getElementById( id );
      if ( !path ) return;

      const bbox  = path.getBBox();
      const cx    = bbox.x + bbox.width  / 2;
      const cy    = bbox.y + bbox.height / 2;
      const count = projects.length;

      const group = document.createElementNS( SVG_NS, 'g' );
      group.setAttribute( 'class', 'ukm-marker-group' );
      group.setAttribute( 'transform', 'translate(' + cx + ',' + cy + ') scale(' + scale + ')' );
      group.setAttribute( 'data-rid', id );
      group.setAttribute( 'tabindex', '0' );
      group.setAttribute( 'role', 'button' );
      group.setAttribute( 'aria-label', ( info.name || id ) + ' \u2014 ' + count + ' project' + ( count !== 1 ? 's' : '' ) );

      if ( markerIcon ) {
        const img = document.createElementNS( SVG_NS, 'image' );
        img.setAttribute( 'href', markerIcon );
        img.setAttribute( 'x', '-16' ); img.setAttribute( 'y', '-32' );
        img.setAttribute( 'width', '32' ); img.setAttribute( 'height', '32' );
        img.setAttribute( 'class', 'ukm-marker__icon' );
        group.appendChild( img );

        const badge = document.createElementNS( SVG_NS, 'circle' );
        badge.setAttribute( 'cx', '14' ); badge.setAttribute( 'cy', '-26' ); badge.setAttribute( 'r', '9' );
        badge.setAttribute( 'class', 'ukm-marker__badge' );
        group.appendChild( badge );

        const badgeText = document.createElementNS( SVG_NS, 'text' );
        badgeText.setAttribute( 'x', '14' ); badgeText.setAttribute( 'y', '-26' );
        badgeText.setAttribute( 'text-anchor', 'middle' );
        badgeText.setAttribute( 'dominant-baseline', 'middle' );
        badgeText.setAttribute( 'class', 'ukm-marker__badge-text' );
        badgeText.textContent = count > 99 ? '99+' : String( count );
        group.appendChild( badgeText );
      } else {
        const pin = document.createElementNS( SVG_NS, 'path' );
        pin.setAttribute( 'd', 'M0,14 C0,14 -16,-2 -16,-12 A16,16 0 1,1 16,-12 C16,-2 0,14 0,14 Z' );
        pin.setAttribute( 'class', 'ukm-marker__pin' );
        pin.style.fill = markerColor;
        group.appendChild( pin );

        const countText = document.createElementNS( SVG_NS, 'text' );
        countText.setAttribute( 'x', '0' ); countText.setAttribute( 'y', '-12' );
        countText.setAttribute( 'text-anchor', 'middle' );
        countText.setAttribute( 'dominant-baseline', 'middle' );
        countText.setAttribute( 'class', 'ukm-marker__count' );
        countText.textContent = count > 99 ? '99+' : String( count );
        group.appendChild( countText );
      }

      group.addEventListener( 'click', function ( e ) { e.stopPropagation(); onActivate( id, group ); } );
      group.addEventListener( 'keydown', function ( e ) {
        if ( e.key === 'Enter' || e.key === ' ' ) { e.preventDefault(); e.stopPropagation(); onActivate( id, group ); }
      } );

      grp.appendChild( group );
    } );
  }

  /* -------------------------------------------------------
     Helpers
  ------------------------------------------------------- */
  function escHtml( str ) {
    return String( str )
      .replace( /&/g,  '&amp;' )
      .replace( /</g,  '&lt;' )
      .replace( />/g,  '&gt;' )
      .replace( /"/g,  '&quot;' )
      .replace( /'/g,  '&#039;' );
  }

}() );
