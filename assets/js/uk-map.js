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

    let activeId   = null;
    let searchTerm = '';

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
      const id   = path.id;
      if ( !id ) return;
      const info = data[ id ] || { name: path.getAttribute( 'name' ) || id, projects: [] };

      if ( info.color ) path.style.fill = info.color;

      path.setAttribute( 'tabindex', '0' );
      path.setAttribute( 'role', 'button' );
      path.setAttribute( 'aria-label', info.name || id );

      path.addEventListener( 'mouseenter', e => showTooltip( e, info.name || id ) );
      path.addEventListener( 'mousemove',  e => moveTooltip( e ) );
      path.addEventListener( 'mouseleave', hideTooltip );

      path.addEventListener( 'click', () => handleActivate( id ) );
      path.addEventListener( 'keydown', e => {
        if ( e.key === 'Enter' || e.key === ' ' ) { e.preventDefault(); handleActivate( id ); }
      } );
    } );

    /* ---- Place SVG markers ---- */
    placeMarkers( svg, data, markerIcon, markerColor, handleActivate );

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
      /* Toggle: clicking the active region deselects it */
      setActive( id === activeId ? null : id );
      renderSidebar();
    }

    function setActive( id ) {
      if ( activeId ) {
        const prev = svg.getElementById( activeId );
        if ( prev ) prev.classList.remove( 'ukm-active' );
        svg.querySelectorAll( `.ukm-marker-group[data-rid="${ activeId }"]` )
           .forEach( m => m.classList.remove( 'ukm-marker--active' ) );
      }
      activeId = id;
      if ( id ) {
        const el = svg.getElementById( id );
        if ( el ) el.classList.add( 'ukm-active' );
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
  function placeMarkers( svg, data, markerIcon, markerColor, onActivate ) {
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
      group.setAttribute( 'transform', 'translate(' + cx + ',' + cy + ')' );
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
          ? '<a class="ukm-project-card__link" href="' + escAttr( p.url ) + '" target="_blank" rel="noopener noreferrer">View Website</a>'
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
