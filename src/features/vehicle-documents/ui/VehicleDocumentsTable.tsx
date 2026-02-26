import Link from "next/link";
import { Plus } from "lucide-react";
import { Table, TBody, THead, TD, TH, TR } from "@/components/ui/Table";
import StatusBadge from "@/components/ui/StatusBadge";
import { buttonClasses } from "@/components/ui/Button";
import type { VehicleDocument } from "@/features/vehicle-documents/domain/types";

export default function VehicleDocumentsTable({
  title,
  description,
  rows,
}: {
  title: string;
  description: string;
  rows: VehicleDocument[];
}) {
  return (
    <div className="space-y-6">
      <div className="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight">{title}</h1>
          <p className="mt-1 text-sm opacity-70">{description}</p>
        </div>

        <Link
          href="/vehicle-documents/new"
          className={buttonClasses({ variant: "primary", size: "md" })}
        >
          <Plus className="h-4 w-4" />
          + Yeni Araç/Şoför Ekle
        </Link>
      </div>

      <section className="space-y-3">
        <div>
          <div className="text-base font-semibold">Evrak Listesi</div>
          <div className="text-sm opacity-70">
            SRC ve Psikoteknik uygunluğu ile hızlı aksiyon
          </div>
        </div>

        <Table>
          <THead>
            <tr>
              <TH>Plaka</TH>
              <TH>Şoför Adı</TH>
              <TH className="tabular-nums">TC Kimlik No</TH>
              <TH>SRC Belgesi</TH>
              <TH>Psikoteknik</TH>
              <TH className="text-right">İşlemler</TH>
            </tr>
          </THead>
          <TBody>
            {rows.map((r) => (
              <TR key={r.id}>
                <TD className="font-medium">{r.plate}</TD>
                <TD>{r.driverName}</TD>
                <TD className="tabular-nums">{r.nationalId}</TD>
                <TD>
                  <StatusBadge variant={r.srcValid ? "ok" : "bad"}>
                    {r.srcValid ? "Geçerli" : "Eksik"}
                  </StatusBadge>
                </TD>
                <TD>
                  <StatusBadge variant={r.psychoValid ? "ok" : "bad"}>
                    {r.psychoValid ? "Geçerli" : "Eksik"}
                  </StatusBadge>
                </TD>
                <TD className="text-right">
                  <div className="inline-flex items-center justify-end gap-2">
                    <Link
                      href={`/vehicle-documents/${r.id}`}
                      className={buttonClasses({ variant: "secondary", size: "sm" })}
                    >
                      Görüntüle
                    </Link>
                    <Link
                      href={`/vehicle-documents/${r.id}/edit`}
                      className={buttonClasses({ variant: "ghost", size: "sm" })}
                    >
                      Düzenle
                    </Link>
                  </div>
                </TD>
              </TR>
            ))}
          </TBody>
        </Table>
      </section>
    </div>
  );
}
