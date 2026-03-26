/* global ukmData */
( function () {
  'use strict';

  document.addEventListener( 'DOMContentLoaded', init );

  const SVG_NS = 'http://www.w3.org/2000/svg';

  /* Shared floating tooltip */
  const tooltip = ( function () {
    const el = document.createElement( 'div' );
    el.className = 'ukm-tooltip';
    document.body.appendChild( el );
    return el;
  }() );

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

    const panel       = wrap.querySelector( '.ukm-side-panel' );
    const data        = ( typeof ukmData !== 'undefined' && ukmData.regions ) ? ukmData.regions : {};
    const markerIcon  = ( typeof ukmData !== 'undefined' ) ? ( ukmData.markerIcon  || '' ) : '';
    const markerColor = ( typeof ukmData !== 'undefined' ) ? ( ukmData.markerColor || '#e74c3c' ) : '#e74c3c';
    const markerSize     = ( typeof ukmData !== 'undefined' ) ? ( ukmData.markerSize     || 32 )        : 32;
    const selectedColor  = ( typeof ukmData !== 'undefined' ) ? ( ukmData.selectedColor  || '#2271b1' )  : '#2271b1';

    let activeId   = null;
    let searchTerm = '';

    /* ---- Zoom + Pan state ---- */
    const mapArea = wrap.querySelector( '.ukm-map-area' );
    const origVB  = svg.getAttribute( 'viewBox' ) || '0 0 1000 1000';
    const vbParts = origVB.split( /[\s,]+/ ).map( Number );

    /* Live viewBox state — updated by both zoom and drag */
    let vbX = vbParts[0], vbY = vbParts[1], vbW = vbParts[2], vbH = vbParts[3];
    let zoomLevel = 1;
    const ZOOM_STEP = 1.35;
    const MAX_ZOOM  = 8;

    function setViewBox( x, y, w, h ) {
      vbX = x; vbY = y; vbW = w; vbH = h;
      svg.setAttribute( 'viewBox', `${ x } ${ y } ${ w } ${ h }` );
    }

    /* ---- Zoom controls ---- */
    const zoomBar = document.createElement( 'div' );
    zoomBar.className = 'ukm-zoom-bar';
    zoomBar.innerHTML =
      '<button class="ukm-zoom-btn" data-z="in"  aria-label="Zoom in">+</button>'
      + '<button class="ukm-zoom-btn" data-z="out" aria-label="Zoom out">&minus;</button>'
      + '<button class="ukm-zoom-btn" data-z="reset" aria-label="Reset zoom" title="Reset">&#8635;</button>';
    mapArea.appendChild( zoomBar );

    zoomBar.addEventListener( 'click', e => {
      const btn = e.target.closest( '[data-z]' );
      if ( !btn ) return;
      const action = btn.dataset.z;
      if ( action === 'reset' ) {
        zoomLevel = 1;
        setViewBox( vbParts[0], vbParts[1], vbParts[2], vbParts[3] );
      } else if ( action === 'in' && zoomLevel < MAX_ZOOM ) {
        zoomLevel *= ZOOM_STEP;
        applyZoom();
      } else if ( action === 'out' && zoomLevel > 1 ) {
        zoomLevel /= ZOOM_STEP;
        if ( zoomLevel < 1 ) zoomLevel = 1;
        applyZoom();
      }
      updateZoomBtn();
    } );

    /* Scroll-wheel zoom (towards cursor position) */
    svg.addEventListener( 'wheel', e => {
      e.preventDefault();
      const rect   = svg.getBoundingClientRect();
      /* cursor position in SVG coordinate space */
      const mx = vbX + ( e.clientX - rect.left ) / rect.width  * vbW;
      const my = vbY + ( e.clientY - rect.top  ) / rect.height * vbH;

      if ( e.deltaY < 0 && zoomLevel < MAX_ZOOM ) {
        zoomLevel *= ZOOM_STEP;
      } else if ( e.deltaY > 0 && zoomLevel > 1 ) {
        zoomLevel /= ZOOM_STEP;
        if ( zoomLevel < 1 ) zoomLevel = 1;
      } else {
        return;
      }
      /* zoom towards cursor: keep mx/my under the cursor */
      const newW = vbParts[2] / zoomLevel;
      const newH = vbParts[3] / zoomLevel;
      const rx   = ( e.clientX - rect.left ) / rect.width;
      const ry   = ( e.clientY - rect.top  ) / rect.height;
      setViewBox( mx - rx * newW, my - ry * newH, newW, newH );
      updateZoomBtn();
    }, { passive: false } );

    function applyZoom() {
      /* zoom keeping current visible centre */
      const cx = vbX + vbW / 2;
      const cy = vbY + vbH / 2;
      const w  = vbParts[2] / zoomLevel;
      const h  = vbParts[3] / zoomLevel;
      setViewBox( cx - w / 2, cy - h / 2, w, h );
    }

    function updateZoomBtn() {
      zoomBar.querySelector( '[data-z="reset"]' ).style.opacity = zoomLevel > 1 ? '1' : '0.4';
    }

    /* ---- Drag / pan ---- */
    let isDragging = false;
    let dragMoved  = false;
    let dragStart  = null;   /* { x, y } in client px */
    let dragOrigX  = 0, dragOrigY = 0;

    svg.style.cursor = 'grab';

    svg.addEventListener( 'mousedown', e => {
      if ( e.button !== 0 ) return;
      isDragging = true;
      dragMoved  = false;
      dragStart  = { x: e.clientX, y: e.clientY };
      dragOrigX  = vbX;
      dragOrigY  = vbY;
      svg.style.cursor = 'grabbing';
      e.preventDefault();   /* prevent text selection */
    } );

    document.addEventListener( 'mousemove', e => {
      if ( !isDragging ) return;
      const dx = e.clientX - dragStart.x;
      const dy = e.clientY - dragStart.y;
      if ( !dragMoved && ( Math.abs( dx ) > 3 || Math.abs( dy ) > 3 ) ) dragMoved = true;
      if ( !dragMoved ) return;
      const ratio = vbW / svg.getBoundingClientRect().width;
      setViewBox( dragOrigX - dx * ratio, dragOrigY - dy * ratio, vbW, vbH );
    } );

    document.addEventListener( 'mouseup', () => {
      if ( !isDragging ) return;
      isDragging = false;
      svg.style.cursor = 'grab';
      /* Reset dragMoved after click fires (click fires before timeout) */
      setTimeout( () => { dragMoved = false; }, 0 );
    } );

    /* Touch pan */
    svg.addEventListener( 'touchstart', e => {
      if ( e.touches.length !== 1 ) return;
      const t = e.touches[0];
      isDragging = true;
      dragMoved  = false;
      dragStart  = { x: t.clientX, y: t.clientY };
      dragOrigX  = vbX;
      dragOrigY  = vbY;
    }, { passive: true } );

    svg.addEventListener( 'touchmove', e => {
      if ( !isDragging || e.touches.length !== 1 ) return;
      const t  = e.touches[0];
      const dx = t.clientX - dragStart.x;
      const dy = t.clientY - dragStart.y;
      if ( !dragMoved && ( Math.abs( dx ) > 3 || Math.abs( dy ) > 3 ) ) {
        dragMoved = true;
        e.preventDefault();   /* only prevent scroll once confirmed drag */
      }
      if ( !dragMoved ) return;
      const ratio = vbW / svg.getBoundingClientRect().width;
      setViewBox( dragOrigX - dx * ratio, dragOrigY - dy * ratio, vbW, vbH );
    }, { passive: false } );

    svg.addEventListener( 'touchend', () => {
      isDragging = false;
      setTimeout( () => { dragMoved = false; }, 0 );
    } );

    /* ---- Build sidebar skeleton (permanent) ---- */
    panel.innerHTML =
      '<div class="ukm-panel__search-wrap">'
      + '<input type="search" class="ukm-panel__search" placeholder="Search projects\u2026" aria-label="Search projects">'
      + '</div>'
      + '<div class="ukm-panel__body"></div>';

    panel.querySelector( '.ukm-panel__search' ).addEventListener( 'input', function () {
      searchTerm = this.value;
      renderSidebar();
    } );

    renderSidebar(); // Show all projects immediately

    /* ---- Bind region paths ---- */
    svg.querySelectorAll( '#features path' ).forEach( path => {
      const id        = path.id;
      if ( !id ) return;
      const info      = data[ id ] || { name: path.getAttribute( 'name' ) || id, projects: [] };
      const baseFill  = info.color       || '';
      const hoverFill = info.hover_color || '';

      if ( baseFill ) path.style.fill = baseFill;

      path.setAttribute( 'tabindex', '0' );
      path.setAttribute( 'role', 'button' );
      path.setAttribute( 'aria-label', info.name || id );

      path.addEventListener( 'mouseenter', e => {
        showTooltip( e, info.name || id );
        if ( !path.classList.contains( 'ukm-active' ) ) {
          path.style.fill = hoverFill || baseFill || '';
        }
      } );
      path.addEventListener( 'mousemove', e => moveTooltip( e ) );
      path.addEventListener( 'mouseleave', () => {
        hideTooltip();
        if ( !path.classList.contains( 'ukm-active' ) ) {
          path.style.fill = baseFill;
        }
      } );

      path.addEventListener( 'click', () => handleActivate( id ) );
      path.addEventListener( 'keydown', e => {
        if ( e.key === 'Enter' || e.key === ' ' ) { e.preventDefault(); handleActivate( id ); }
      } );
    } );

    /* ---- Place SVG markers ---- */
    placeMarkers( svg, data, markerIcon, markerColor, markerSize, handleActivate );

    /* ---- Deselect when clicking outside the widget ---- */
    document.addEventListener( 'click', e => {
      if ( activeId && !wrap.contains( e.target ) ) {
        handleActivate( null );
      }
    } );

    /* =====================================================
       Closures (share activeId / searchTerm / svg / panel)
    ===================================================== */

    function handleActivate( id ) {
      if ( dragMoved ) return;   /* was a drag, not a click — ignore */
      setActive( id === activeId ? null : id );
      renderSidebar();
    }

    function setActive( id ) {
      if ( activeId ) {
        const prev     = svg.getElementById( activeId );
        const prevInfo = data[ activeId ];
        if ( prev ) {
          prev.classList.remove( 'ukm-active' );
          /* restore base fill when deselecting */
          prev.style.fill = ( prevInfo && prevInfo.color ) ? prevInfo.color : '';
        }
        svg.querySelectorAll( `.ukm-marker-group[data-rid="${ activeId }"]` )
           .forEach( m => m.classList.remove( 'ukm-marker--active' ) );
      }
      activeId = id;
      if ( id ) {
        const el = svg.getElementById( id );
        if ( el ) {
          el.classList.add( 'ukm-active' );
          /* apply selected fill color */
          el.style.fill = selectedColor;
        }
        svg.querySelectorAll( `.ukm-marker-group[data-rid="${ id }"]` )
           .forEach( m => m.classList.add( 'ukm-marker--active' ) );
      }
    }

    /* ---- Sidebar render ---- */
    function renderSidebar() {
      const body = panel.querySelector( '.ukm-panel__body' );

      /* Gather every project across all regions (sorted by region name) */
      const allProjects = [];
      Object.keys( data )
        .sort( ( a, b ) => ( data[a].name || a ).localeCompare( data[b].name || b ) )
        .forEach( id => {
          const info     = data[ id ];
          const projects = info.projects || [];
          projects.forEach( p => {
            allProjects.push( Object.assign( {}, p, { _rid: id, _rname: info.name || id } ) );
          } );
        } );

      /* Filter by selected region */
      let filtered = activeId ? allProjects.filter( p => p._rid === activeId ) : allProjects;

      /* Filter by search term */
      if ( searchTerm.trim() ) {
        const term = searchTerm.toLowerCase();
        filtered = filtered.filter( p =>
          ( p.title       || '' ).toLowerCase().includes( term ) ||
          ( p.description || '' ).toLowerCase().includes( term ) ||
          ( p.location    || '' ).toLowerCase().includes( term ) ||
          ( p._rname      || '' ).toLowerCase().includes( term )
        );
      }

      /* Header */
      let html = '<div class="ukm-panel__header">';
      if ( activeId ) {
        const rname = ( data[ activeId ] && data[ activeId ].name ) || activeId;
        html += '<span class="ukm-panel__heading">' + escHtml( rname ) + '</span>'
              + '<button class="ukm-panel__all-btn" aria-label="Show all projects">\u2190 All</button>';
      } else {
        html += '<span class="ukm-panel__heading">All Projects</span>'
              + '<span class="ukm-panel__total">' + allProjects.length + '</span>';
      }
      html += '</div>';

      /* Cards */
      if ( !filtered.length ) {
        html += '<p class="ukm-panel__empty">'
              + ( searchTerm.trim()
                  ? 'No results for \u201c' + escHtml( searchTerm ) + '\u201d.'
                  : activeId ? 'No projects in this region.' : 'No projects added yet.' )
              + '</p>';
      } else {
        html += '<div class="ukm-panel__projects">';
        filtered.forEach( p => { html += buildCard( p, !activeId ); } );
        html += '</div>';
      }

      body.innerHTML = html;

      const allBtn = body.querySelector( '.ukm-panel__all-btn' );
      if ( allBtn ) {
        allBtn.addEventListener( 'click', () => {
          setActive( null );
          renderSidebar();
        } );
      }
    }
  }

  /* -------------------------------------------------------
     SVG marker placement
     Shows a teardrop pin (or custom icon) with project count
     for every region that has ≥1 project with a location.
  ------------------------------------------------------- */
  function placeMarkers( svg, data, markerIcon, markerColor, markerSize, onActivate ) {
    /* markerSize is in "px equivalent" units; base pin is drawn at 32 SVG-unit scale */
    const scale = ( markerSize || 32 ) / 32;
    let grp = svg.getElementById( 'ukm-markers' );
    if ( !grp ) {
      grp = document.createElementNS( SVG_NS, 'g' );
      grp.id = 'ukm-markers';
      svg.appendChild( grp );
    }

    Object.keys( data ).forEach( id => {
      const info     = data[ id ];
      const projects = info.projects || [];

      /* Only place a marker if at least one project has a location text */
      if ( !projects.some( p => p.location && p.location.trim() ) ) return;

      const path = svg.getElementById( id );
      if ( !path ) return;

      const bbox  = path.getBBox();
      const cx    = bbox.x + bbox.width  / 2;
      const cy    = bbox.y + bbox.height / 2;
      const count = projects.length;
      const label = ( info.name || id ) + ' \u2014 ' + count + ' project' + ( count !== 1 ? 's' : '' );

      const group = document.createElementNS( SVG_NS, 'g' );
      group.setAttribute( 'class', 'ukm-marker-group' );
      group.setAttribute( 'transform', 'translate(' + cx + ',' + cy + ') scale(' + scale + ')' );
      group.setAttribute( 'data-rid', id );
      group.setAttribute( 'tabindex', '0' );
      group.setAttribute( 'role', 'button' );
      group.setAttribute( 'aria-label', label );

      if ( markerIcon ) {
        /* ---- Custom icon + count badge ---- */
        const img = document.createElementNS( SVG_NS, 'image' );
        img.setAttribute( 'href', markerIcon );
        img.setAttribute( 'x',  '-16' );
        img.setAttribute( 'y',  '-32' );
        img.setAttribute( 'width',  '32' );
        img.setAttribute( 'height', '32' );
        img.setAttribute( 'class', 'ukm-marker__icon' );
        group.appendChild( img );

        const badge = document.createElementNS( SVG_NS, 'circle' );
        badge.setAttribute( 'cx', '14' );
        badge.setAttribute( 'cy', '-26' );
        badge.setAttribute( 'r',  '9' );
        badge.setAttribute( 'class', 'ukm-marker__badge' );
        group.appendChild( badge );

        const badgeText = document.createElementNS( SVG_NS, 'text' );
        badgeText.setAttribute( 'x', '14' );
        badgeText.setAttribute( 'y', '-26' );
        badgeText.setAttribute( 'text-anchor', 'middle' );
        badgeText.setAttribute( 'dominant-baseline', 'middle' );
        badgeText.setAttribute( 'class', 'ukm-marker__badge-text' );
        badgeText.textContent = count > 99 ? '99+' : String( count );
        group.appendChild( badgeText );
      } else {
        /* ---- Default teardrop pin ---- */
        /* Pin shape: rounded top centred at (0,-14), tip at (0,14) */
        const pin = document.createElementNS( SVG_NS, 'path' );
        pin.setAttribute( 'd', 'M0,14 C0,14 -16,-2 -16,-12 A16,16 0 1,1 16,-12 C16,-2 0,14 0,14 Z' );
        pin.setAttribute( 'class', 'ukm-marker__pin' );
        pin.style.fill = markerColor;
        group.appendChild( pin );

        /* Drop shadow ring (pure CSS class, no inline fill) */
        const countText = document.createElementNS( SVG_NS, 'text' );
        countText.setAttribute( 'x', '0' );
        countText.setAttribute( 'y', '-12' );
        countText.setAttribute( 'text-anchor', 'middle' );
        countText.setAttribute( 'dominant-baseline', 'middle' );
        countText.setAttribute( 'class', 'ukm-marker__count' );
        countText.textContent = count > 99 ? '99+' : String( count );
        group.appendChild( countText );
      }

      /* Events */
      group.addEventListener( 'click', e => { e.stopPropagation(); onActivate( id ); } );
      group.addEventListener( 'keydown', e => {
        if ( e.key === 'Enter' || e.key === ' ' ) { e.preventDefault(); e.stopPropagation(); onActivate( id ); }
      } );
      group.addEventListener( 'mouseenter', e => showTooltip( e, label ) );
      group.addEventListener( 'mousemove',  e => moveTooltip( e ) );
      group.addEventListener( 'mouseleave', hideTooltip );

      grp.appendChild( group );
    } );
  }

  /* -------------------------------------------------------
     Project card HTML
  ------------------------------------------------------- */
  function buildCard( p, showRegion ) {
    return '<article class="ukm-project-card">'
      + ( showRegion && p._rname
          ? '<div class="ukm-project-card__region">' + escHtml( p._rname ) + '</div>'
          : '' )
      + ( p.image
          ? '<img class="ukm-project-card__image" src="' + escAttr( p.image ) + '" alt="' + escAttr( p.title || '' ) + '">'
          : '' )
      + '<div class="ukm-project-card__body">'
      + ( p.title       ? '<div class="ukm-project-card__title">'    + escHtml( p.title )       + '</div>' : '' )
      + ( p.location    ? '<div class="ukm-project-card__location">&#x1F4CD; ' + escHtml( p.location ) + '</div>' : '' )
      + ( p.description ? '<div class="ukm-project-card__desc">'     + escHtml( p.description ) + '</div>' : '' )
      + ( p.url
          ? '<a class="ukm-project-card__link" href="' + escAttr( p.url ) + '" target="_blank" rel="noopener noreferrer">View Project</a>'
          : '' )
      + '</div></article>';
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
    tooltip.style.left = ( e.clientX - tooltip.offsetWidth / 2 ) + 'px';
    tooltip.style.top  = ( e.clientY - tooltip.offsetHeight - 14 ) + 'px';
  }
  function hideTooltip() {
    tooltip.classList.remove( 'ukm-tooltip--visible' );
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
  function escAttr( str ) {
    return encodeURI( String( str ) );
  }

}() );
