export default function ShipIcon({ className }: { className?: string }) {
  return (
    <svg
      viewBox="0 0 48 40"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      className={className}
    >
      {/* Hull */}
      <path
        d="M4 28 L8 34 H40 L44 28 Z"
        stroke="currentColor"
        strokeWidth="1.2"
        fill="currentColor"
        fillOpacity="0.06"
        strokeLinejoin="round"
      />

      {/* Hull waterline */}
      <line x1="10" y1="32" x2="38" y2="32" stroke="currentColor" strokeWidth="0.6" opacity="0.3" />

      {/* Deck */}
      <rect x="10" y="22" width="28" height="6" rx="1" stroke="currentColor" strokeWidth="1" fill="currentColor" fillOpacity="0.04" />

      {/* Bridge / superstructure */}
      <rect x="16" y="14" width="16" height="8" rx="1" stroke="currentColor" strokeWidth="1" fill="currentColor" fillOpacity="0.06" />

      {/* Bridge windows */}
      <rect x="18" y="16" width="3" height="2.5" rx="0.5" stroke="currentColor" strokeWidth="0.7" fill="currentColor" fillOpacity="0.08" />
      <rect x="22.5" y="16" width="3" height="2.5" rx="0.5" stroke="currentColor" strokeWidth="0.7" fill="currentColor" fillOpacity="0.08" />
      <rect x="27" y="16" width="3" height="2.5" rx="0.5" stroke="currentColor" strokeWidth="0.7" fill="currentColor" fillOpacity="0.08" />

      {/* Funnel / smokestack */}
      <rect x="22" y="8" width="4" height="6" rx="0.5" stroke="currentColor" strokeWidth="0.9" fill="currentColor" fillOpacity="0.08" />
      <line x1="22" y1="10" x2="26" y2="10" stroke="currentColor" strokeWidth="0.6" opacity="0.4" />

      {/* Mast */}
      <line x1="24" y1="4" x2="24" y2="8" stroke="currentColor" strokeWidth="0.8" opacity="0.5" />
      <circle cx="24" cy="4" r="0.8" fill="currentColor" opacity="0.5" />

      {/* Bow detail */}
      <line x1="38" y1="24" x2="42" y2="28" stroke="currentColor" strokeWidth="0.6" opacity="0.3" />

      {/* Fuel intake port (left side - where tanker connects) */}
      <circle cx="8" cy="26" r="1.5" stroke="currentColor" strokeWidth="0.8" fill="currentColor" fillOpacity="0.15" />
    </svg>
  );
}
