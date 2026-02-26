create extension if not exists pgcrypto;

create table if not exists public.ports (
  id uuid primary key default gen_random_uuid(),
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),

  name text not null,
  country text null,

  rules text null,
  agency_name text null,
  agency_phone text null,
  agency_email text null,
  agency_address text null,

  required_documents jsonb not null default '[]'::jsonb
);

create index if not exists ports_name_idx on public.ports (name);

create table if not exists public.assets (
  id uuid primary key default gen_random_uuid(),
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),

  truck_plate text not null,
  driver_full_name text null,
  driver_identity_no text null,
  driver_document_no text null,
  driver_phone text null
);

create unique index if not exists assets_truck_plate_uq on public.assets (truck_plate);

create table if not exists public.operations (
  id uuid primary key default gen_random_uuid(),
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),

  vessel_name text not null,
  quantity_mt numeric(12, 3) not null,
  operation_date date not null,

  status text not null default 'active',

  port_id uuid not null references public.ports (id) on delete restrict,
  asset_id uuid null references public.assets (id) on delete set null
);

create index if not exists operations_port_id_idx on public.operations (port_id);
create index if not exists operations_asset_id_idx on public.operations (asset_id);
create index if not exists operations_operation_date_idx on public.operations (operation_date);
create index if not exists operations_status_idx on public.operations (status);

