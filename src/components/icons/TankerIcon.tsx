export default function TankerIcon({ className }: { className?: string }) {
  return (
    <svg
      viewBox="0 0 80 32"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      className={className}
    >
      <defs>
        <linearGradient id="tankGrad" x1="0" y1="4" x2="0" y2="18">
          <stop offset="0%" stopColor="currentColor" stopOpacity="0.15" />
          <stop offset="100%" stopColor="currentColor" stopOpacity="0.05" />
        </linearGradient>
        <linearGradient id="cabinGrad" x1="0" y1="4" x2="0" y2="18">
          <stop offset="0%" stopColor="currentColor" stopOpacity="0.12" />
          <stop offset="100%" stopColor="currentColor" stopOpacity="0.04" />
        </linearGradient>
      </defs>

      {/* Chassis / undercarriage */}
      <rect x="3" y="18" width="56" height="2" rx="1" fill="currentColor" opacity="0.2" />

      {/* Tank body */}
      <rect x="2" y="5" width="44" height="13" rx="6.5" stroke="currentColor" strokeWidth="1.1" fill="url(#tankGrad)" />

      {/* Tank highlight line */}
      <path d="M8 8 H38" stroke="currentColor" strokeWidth="0.6" opacity="0.3" strokeLinecap="round" />

      {/* Tank bands */}
      <line x1="16" y1="5.5" x2="16" y2="17.5" stroke="currentColor" strokeWidth="0.7" opacity="0.25" />
      <line x1="30" y1="5.5" x2="30" y2="17.5" stroke="currentColor" strokeWidth="0.7" opacity="0.25" />

      {/* Tank cap */}
      <rect x="21" y="2" width="4" height="3.5" rx="1" stroke="currentColor" strokeWidth="0.8" fill="currentColor" fillOpacity="0.1" />

      {/* Connection between tank and cabin */}
      <rect x="46" y="10" width="4" height="8" rx="0.5" fill="currentColor" opacity="0.15" />
      <rect x="46" y="10" width="4" height="8" rx="0.5" stroke="currentColor" strokeWidth="0.7" fill="none" opacity="0.3" />

      {/* Cabin body */}
      <path d="M50 6 H64 Q66 6 66 8 V18 H50 V6Z" stroke="currentColor" strokeWidth="1.1" fill="url(#cabinGrad)" strokeLinejoin="round" />

      {/* Cabin windshield */}
      <path d="M55 8 H63 Q64 8 64 9 V13 H55 V8Z" stroke="currentColor" strokeWidth="0.8" fill="currentColor" fillOpacity="0.08" strokeLinejoin="round" />

      {/* Windshield reflection */}
      <line x1="57" y1="8.5" x2="57" y2="12.5" stroke="currentColor" strokeWidth="0.4" opacity="0.2" />

      {/* Side mirror */}
      <rect x="66.5" y="9" width="2.5" height="1.5" rx="0.5" stroke="currentColor" strokeWidth="0.6" fill="none" opacity="0.5" />

      {/* Bumper */}
      <rect x="66" y="15" width="3" height="3" rx="0.5" stroke="currentColor" strokeWidth="0.7" fill="currentColor" fillOpacity="0.08" />

      {/* Headlight */}
      <circle cx="68" cy="12" r="1" fill="currentColor" opacity="0.5" />

      {/* Exhaust pipe */}
      <rect x="0" y="14" width="2.5" height="1.5" rx="0.5" fill="currentColor" opacity="0.3" />

      {/* Wheel 1 - rear left */}
      <circle cx="13" cy="23" r="5" stroke="currentColor" strokeWidth="1" fill="currentColor" fillOpacity="0.06" />
      <circle cx="13" cy="23" r="2.5" stroke="currentColor" strokeWidth="0.7" fill="none" opacity="0.5" />
      <circle cx="13" cy="23" r="0.8" fill="currentColor" opacity="0.4" />
      <g className="tanker-wheel-spin" style={{ transformOrigin: "13px 23px" }}>
        <line x1="13" y1="18.5" x2="13" y2="27.5" stroke="currentColor" strokeWidth="0.4" opacity="0.2" />
        <line x1="8.5" y1="23" x2="17.5" y2="23" stroke="currentColor" strokeWidth="0.4" opacity="0.2" />
      </g>

      {/* Wheel 2 - rear right */}
      <circle cx="33" cy="23" r="5" stroke="currentColor" strokeWidth="1" fill="currentColor" fillOpacity="0.06" />
      <circle cx="33" cy="23" r="2.5" stroke="currentColor" strokeWidth="0.7" fill="none" opacity="0.5" />
      <circle cx="33" cy="23" r="0.8" fill="currentColor" opacity="0.4" />
      <g className="tanker-wheel-spin" style={{ transformOrigin: "33px 23px" }}>
        <line x1="33" y1="18.5" x2="33" y2="27.5" stroke="currentColor" strokeWidth="0.4" opacity="0.2" />
        <line x1="28.5" y1="23" x2="37.5" y2="23" stroke="currentColor" strokeWidth="0.4" opacity="0.2" />
      </g>

      {/* Wheel 3 - front (cabin) */}
      <circle cx="58" cy="23" r="5" stroke="currentColor" strokeWidth="1" fill="currentColor" fillOpacity="0.06" />
      <circle cx="58" cy="23" r="2.5" stroke="currentColor" strokeWidth="0.7" fill="none" opacity="0.5" />
      <circle cx="58" cy="23" r="0.8" fill="currentColor" opacity="0.4" />
      <g className="tanker-wheel-spin" style={{ transformOrigin: "58px 23px" }}>
        <line x1="58" y1="18.5" x2="58" y2="27.5" stroke="currentColor" strokeWidth="0.4" opacity="0.2" />
        <line x1="53.5" y1="23" x2="62.5" y2="23" stroke="currentColor" strokeWidth="0.4" opacity="0.2" />
      </g>
    </svg>
  );
}
