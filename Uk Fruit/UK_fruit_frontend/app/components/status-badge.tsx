import { Chip } from "@mui/material";

const statusColors: Record<string, { color: string; background: string }> = {
  Completed: { color: "#397850", background: "#edf6ef" },
  Processing: { color: "#a77a34", background: "#faf5e8" },
  Pending: { color: "#64849a", background: "#edf4f8" },
  Active: { color: "#397850", background: "#edf6ef" },
  Cancelled: { color: "#a75f5f", background: "#fbefef" },
};

export function StatusBadge({ status }: { status: string }) {
  const colors = statusColors[status] ?? statusColors.Pending;
  return (
    <Chip
      size="small"
      label={status}
      sx={{ height: 22, color: colors.color, bgcolor: colors.background, fontSize: 11, "& .MuiChip-label": { px: 1 } }}
    />
  );
}
